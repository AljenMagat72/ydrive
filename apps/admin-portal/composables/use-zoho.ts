import { useAPI } from '#imports';

interface ZohoDriverData {
  [key: string]: any[]; 
}

export function useZoho() {
  const { get, post } = useAPI();
  const isLoading = ref(false);
  const config = useRuntimeConfig();

  const checkIsExpired = (dateString: string | undefined) => {
    if (!dateString || dateString === '---') return false;
    
    const expDate = new Date(dateString);
    const today = new Date();
    
    // Reset time to midnight to compare just the date
    expDate.setHours(0, 0, 0, 0);
    today.setHours(0, 0, 0, 0);
    
    // If the expiration date is today or in the past, it's expired
    return expDate <= today; 
  };

  async function fetchZohoDetails(zohoId: string): Promise<ZohoDriverData | null> {
    if (!zohoId) return null;
    isLoading.value = true;
    try {
      const response = await get<{ success: boolean; data: ZohoDriverData }>(
        `/admin/driver-details/${zohoId}`
      );
      return response?.success ? response.data : null;
    } catch (error) {
      console.error("Fetch Error:", error);
      return null;
    } finally {
      isLoading.value = false;
    }
  }

  function getAttachmentUrl(zohoId: string, fileId: string) {
    const base = config.public.apiBase; 
    return `${base}/admin/view-attachment/${zohoId}/${fileId}`;
  }

  async function downloadAttachmentsZip(zohoId: string, fileIds: string[], driverName: string) {
    if (!zohoId || fileIds.length === 0) return;
    isLoading.value = true;

    try {
      const response = await post<Blob>(
        `/admin/driver-documents-zip/${zohoId}`, 
        { file_ids: fileIds },
        { 'Accept': 'application/zip' } 
      );

      const blob = new Blob([response as any], { type: 'application/zip' });
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      
      link.href = url;
      link.setAttribute('download', `Documents_${driverName.replace(/\s+/g, '_')}.zip`);
      document.body.appendChild(link);
      link.click();
      
      document.body.removeChild(link);
      window.URL.revokeObjectURL(url);
    } catch (error) {
      console.error("Zip Download Error:", error);
    } finally {
      isLoading.value = false;
    }
  }

  return { 
    isLoading, 
    fetchZohoDetails, 
    getAttachmentUrl, 
    downloadAttachmentsZip,
    checkIsExpired,
    
    getExpirationStatus: (data: ZohoDriverData) => {
      const complianceFields = [
        'License_Exp', 
        'City_License_Exp', 
        'Criminal_Check_Exp', 
        'Abstract_Exp', 
        'Insurance_Exp', 
        'Registration_Exp', 
        'Safety_Exp'
      ];

      return Object.values(data).flat().some(item => {
        return complianceFields.some(field => checkIsExpired(item[field]));
      });
    }
  };
}