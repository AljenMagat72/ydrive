import { computed, readonly, ref } from 'vue';
import { useAuth, useAPI, useAuthToken } from '#imports';

interface ZohoDriver {
  id?: string;
  Full_Name?: string;
  City?: string;
  Phone?: string;
  Date_of_Birth?: string;
  Make?: string;
  Model?: string;
  Year?: string | number;
  Bank_Name?: string;
  Bank_Account?: string;
  Transit?: string;
  Institution?: string;
  HSTGST?: string;
  License_Class?: string;
  License_Exp?: string;
  City_License_Exp?: string;
  Criminal_Check_Exp?: string;
  Abstract_Exp?: string;
  Insurance_Exp?: string;
  Registration_Exp?: string;
  Safety_Exp?: string;
  [key: string]: any;
}


const driverDetails = ref<ZohoDriver | null>(null);
const isLoading = ref<boolean>(false);

export function useZoho() {
  const { user } = useAuth();
  const { get, post } = useAPI();
  const authToken = useAuthToken();

  async function fetchZohoDetails() {
    const zohoId = user.value?.zoho_id;
    if (!zohoId || isLoading.value) return;

    isLoading.value = true;
    try {
      const response = await get('/api/driver-details');
      
      if (response && response.success) {
        driverDetails.value = response.data;
      }
    } catch (error) {
      console.error("Zoho Fetch Error:", error);
    } finally {
      isLoading.value = false;
    }
  }

    async function fetchSecureImage(fileId: string) {
    if (!authToken.value || !fileId) return null;

    try {
      const response = await get<Blob>(`/api/view-attachment/${fileId}`);

      if (!response || response.size === 0) {
        console.warn(`Attachment ${fileId} returned an empty blob.`);
        return null;
      }

      return URL.createObjectURL(response);
    } catch (e) {
      console.error("Secure Image Fetch Error:", e);
      return null;
    }
  }

  async function viewAttachment(fileId: string) {
    try {
      const response = await get<Blob>(`/api/view-attachment/${fileId}`, {}, {
      });
      
      return response;
    } catch (error) {
      console.error("Error fetching attachment:", error);
      throw error;
    }
  }

  async function uploadDocuments(files: File[], documentType: string = 'General Document') {
    if (!files.length) return;

    const formData = new FormData();
    
    formData.append('document_type', documentType);
    
    files.forEach((file) => {
      formData.append('document[]', file);
    });

    try {
      const response = await post('/api/zoho/update-document', formData);
      return response;
    } catch (error: any) {
      console.error(`Upload Error (${documentType}):`, error);
      throw error;
    }
  }

  async function updateProfile(payload: Record<string, any>) {
  isLoading.value = true;
  try {
    const response = await post('/api/zoho/update-profile', payload);
    
    if (response && response.success) {
      await fetchZohoDetails();
    }
    return response;
  } catch (error) {
    console.error("Zoho Profile Update Error:", error);
    throw error;
  } finally {
    isLoading.value = false;
  }
}

  async function logout() {
    driverDetails.value = null;
  }

  return {
    driverDetails: readonly(driverDetails),
    isLoading: readonly(isLoading),
    fetchZohoDetails,
    fetchSecureImage,
    viewAttachment,
    uploadDocuments,
    updateProfile,
    logout,
    
    // --- Identification & Personal ---
    fullName: computed(() => driverDetails.value?.Full_Name || '---'),
    phone: computed(() => driverDetails.value?.Phone || '---'),
    dob: computed(() => driverDetails.value?.Date_of_Birth || '---'),
    city: computed(() => driverDetails.value?.City || '---'),

    // --- Vehicle Details ---
    make: computed(() => driverDetails.value?.Make || '---'),
    model: computed(() => driverDetails.value?.Model || '---'),
    year: computed(() => driverDetails.value?.Year || '---'),

    // --- Banking & Tax ---
    bankName: computed(() => driverDetails.value?.Bank_Name || '---'),
    bankAccount: computed(() => driverDetails.value?.Bank_Account || '---'),
    transit: computed(() => driverDetails.value?.Transit || '---'),
    institution: computed(() => driverDetails.value?.Institution || '---'),
    hstGst: computed(() => driverDetails.value?.HSTGST || '---'),

    // --- Licenses & Compliance ---
    licenseClass: computed(() => driverDetails.value?.License_Class || '---'),
    licenseExp: computed(() => driverDetails.value?.License_Exp || '---'),
    cityLicenseExp: computed(() => driverDetails.value?.City_License_Exp || '---'),

    // --- Document Expiries ---
    criminalCheckExp: computed(() => driverDetails.value?.Criminal_Check_Exp || '---'),
    abstractExp: computed(() => driverDetails.value?.Abstract_Exp || '---'),
    insuranceExp: computed(() => driverDetails.value?.Insurance_Exp || '---'),
    registrationExp: computed(() => driverDetails.value?.Registration_Exp || '---'),
    safetyExp: computed(() => driverDetails.value?.Safety_Exp || '---'),
  };
}