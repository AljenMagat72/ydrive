import { computed, onMounted, ref } from 'vue';

export type ResolvedAppearance = 'light' | 'dark';
type Appearance = ResolvedAppearance | 'system';

async function circleRevealTransition(
  callback: () => void,
  event?: MouseEvent
) {
  if (!document.startViewTransition) {
    callback();
    return;
  }

  const x = event?.clientX ?? window.innerWidth / 2;
  const y = event?.clientY ?? window.innerHeight / 2;

  const endRadius = Math.hypot(
    Math.max(x, window.innerWidth - x),
    Math.max(y, window.innerHeight - y)
  );

  const transition = document.startViewTransition(() => {
    callback();
  });

  await transition.ready;

  document.documentElement.animate(
    {
      clipPath: [
        `circle(0px at ${x}px ${y}px)`,
        `circle(${endRadius}px at ${x}px ${y}px)`
      ]
    },
    {
      duration: 400,
      easing: 'ease-in-out',
      pseudoElement: '::view-transition-new(root)'
    }
  );
}

export function updateTheme(value: Appearance) {
  if (typeof window === 'undefined') {
    return;
  }

  if (value === 'system') {
    const mediaQueryList = window.matchMedia(
      '(prefers-color-scheme: dark)',
    );
    const systemTheme = mediaQueryList.matches ? 'dark' : 'light';

    document.documentElement.classList.toggle(
      'dark',
      systemTheme === 'dark',
    );
  } else {
    document.documentElement.classList.toggle('dark', value === 'dark');
  }
}

const mediaQuery = () => {
  if (typeof window === 'undefined') {
    return null;
  }

  return window.matchMedia('(prefers-color-scheme: dark)');
};

const getStoredAppearance = () => {
  if (typeof window === 'undefined') {
    return null;
  }

  return localStorage.getItem('appearance') as Appearance | null;
};

const prefersDark = (): boolean => {
  if (typeof window === 'undefined') {
    return false;
  }

  return window.matchMedia('(prefers-color-scheme: dark)').matches;
};

const handleSystemThemeChange = () => {
  const currentAppearance = getStoredAppearance();

  updateTheme(currentAppearance || 'system');
};

export function initializeTheme() {
  if (typeof window === 'undefined') {
    return;
  }

  const savedAppearance = getStoredAppearance();
  console.log(savedAppearance);
  updateTheme(savedAppearance || 'system');

  mediaQuery()?.addEventListener('change', handleSystemThemeChange);
}

const appearance = ref<Appearance>('system');

export function useAppearance() {
  onMounted(() => {
    const savedAppearance = localStorage.getItem(
      'appearance',
    ) as Appearance | null;

    if (savedAppearance) {
      appearance.value = savedAppearance;
    }
  });

  const resolvedAppearance = computed<ResolvedAppearance>(() => {
    if (appearance.value === 'system') {
      return prefersDark() ? 'dark' : 'light';
    }

    return appearance.value;
  });

  function updateAppearance(value: Appearance, event?: MouseEvent) {
    circleRevealTransition(() => {
      appearance.value = value;

      localStorage.setItem('appearance', value);

      updateTheme(value);
    }, event);
  }

  function toggleAppearance(event?: MouseEvent) {
    const next =
      resolvedAppearance.value === 'dark' ? 'light' : 'dark';

    updateAppearance(next, event);
  }

  return {
    appearance,
    resolvedAppearance,
    toggleAppearance,
    updateAppearance,
  };
}
