import puppeteer from 'puppeteer';

const keys = new Map<string, string>([
  ['v_dim_driver_1.driver_id', 'id'],
  ['hourly_driver_measures.total_sent_offers_pulled_offers', 'total_offers'],
  ['hourly_driver_measures.total_accepted_offers', 'accepted_offers'],
  ['hourly_driver_measures.total_expired_offers', 'expired_offers'],
  ['hourly_driver_measures.total_rejected_offers', 'rejected_offers'],
  ['hourly_driver_measures.acceptance_rate', 'acceptance_rate'],
  ['hourly_driver_measures.total_completed_rides', 'completed_rides'],
  ['hourly_driver_measures.total_canceled_rides', 'cancelled_rides']
]);

export type DriverAnalytics = {
  id: string;
  total_offers: number;
  accepted_offers: number;
  expired_offers: number;
  rejected_offers: number;
  acceptance_rate: number;
  completed_rides: number;
  cancelled_rides: number;
}

function parseDriverAnalytics(raw: Record<string, string>): DriverAnalytics {
  return {
    id: raw.id!,
    total_offers: parseFloat(raw.total_offers!) || 0,
    accepted_offers: parseFloat(raw.accepted_offers!) || 0,
    expired_offers: parseFloat(raw.expired_offers!) || 0,
    rejected_offers: parseFloat(raw.rejected_offers!) || 0,
    acceptance_rate: parseFloat(raw.acceptance_rate!) || 0,
    completed_rides: parseFloat(raw.completed_rides!) || 0,
    cancelled_rides: parseFloat(raw.cancelled_rides!) || 0,
  };
}

export async function scrape(): Promise<DriverAnalytics[]> {

  const username = process.env.USERNAME;
  const password = process.env.PASSWORD;

  if (!username || !password) {
    throw new Error('no credientials provided');
  }

  const browser = await puppeteer.launch({
    args: ['--no-sandbox'],
    slowMo: 50,
  });

  try {
    const page = await browser.newPage();
    await page.goto('https://control.autofleet.io/login');
    
    await page.waitForSelector('#loginName', { visible: true });
    await page.waitForSelector('#loginPassword', { visible: true });

    await page.type('#loginName', username);
    await page.type('#loginPassword', password);
    await page.click('#loginSubmit');

    try {
      await page.waitForSelector('nav', { timeout: 15000 });
    } catch (e) {
      throw new Error('failed to login');
    }

    await page.waitForSelector('[data-tooltip-content="Reports"]');
    await page.click('[data-tooltip-content="Reports"]');

    await page.waitForSelector('[data-tooltip-content="Drivers"]');
    await page.click('[data-tooltip-content="Drivers"]');

    await page.waitForSelector('[data-tooltip-content="Analytics"]');
    await page.click('[data-tooltip-content="Analytics"]');

    await page.waitForSelector('iframe[src*="/login/embed"]');

    const iframeElement = await page.$('iframe[src*="/login/embed"]');
    console.log('found frame');
    
    if (!iframeElement) {
      throw new Error('iframe not found');
    }

    console.log('getting frame contents');
    const frame = await iframeElement.contentFrame();

    if (!frame) {
      throw new Error('Could not access iframe content');
    }

    console.log('await tooltip');
    await frame.waitForSelector('[data-title="Drivers Performances"]', { timeout: 15000 });

    await frame.evaluate(() => {
      //@ts-ignore
      const element = document.querySelector('[data-title="Drivers Performances"]');
      if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });

    await frame.waitForSelector('[col-id="v_dim_driver_1.driver_id"]');

    const rawRowsData = await frame.evaluate(async (keysArray) => {
      //@ts-ignore
      const titleElement = document.querySelector('[data-title="Drivers Performances"]');
      //@ts-ignore
      const grandparent = titleElement?.parentElement?.parentElement;
      //@ts-ignore
      const viewport = grandparent?.querySelector('.ag-body-viewport');

      const allData: any[] = [];
      let rowIndex = 0;

      while (true) {
        //@ts-ignore
        const row = grandparent?.querySelector(`.ag-center-cols-container [row-id="${rowIndex}"]`);
        
        if (!row) {
          console.log('no more rows at index', rowIndex);
          break;
        }

        //@ts-ignore
        viewport.scrollTop = rowIndex * 21;

        //@ts-ignore
        await new Promise(resolve => requestAnimationFrame(resolve));

        const rowData: any = {};

        for (const [colId, fieldName] of keysArray) {
          //@ts-ignore
          const cell = row.querySelector(`[col-id="${colId}"]`);
          if (cell) {
            //@ts-ignore
            rowData[fieldName] = cell.textContent?.trim();
          }
        }

        allData.push(rowData);
        rowIndex++;
      }

      return allData;
    }, Array.from(keys.entries()));

    // Convert raw string data to typed DriverAnalytics objects
    const drivers = rawRowsData.map(parseDriverAnalytics);
    
    console.log(`Scraped ${drivers.length} drivers`);
    return drivers;
  }
  finally {
    await browser.close();
  }
}