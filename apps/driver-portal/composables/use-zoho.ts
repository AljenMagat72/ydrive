import { computed, readonly, ref } from 'vue';
import { useAuth, useAPI, useAuthToken } from '#imports';

const driverDetails = ref<ZohoDriver | null>(null);
const documents = ref<any[]>([]);
const isDocsLoading = ref<boolean>(false);

interface ZohoDriver {
  DL_Number?: string;
  Expiry_Date?: string;
  Full_Name?: string;
  Account?: string;
  License_Exp?: string;
  Hiring_Stage?: string;
  Make?: string;
  Model?: string;
  Insurance_Exp?: string;
  [key: string]: any;
}

export function useZoho() {
  const { user } = useAuth();
  const { get } = useAPI();
  const authToken = useAuthToken();

  // 1. Switch to ref. This will be null on every page refresh/load.
  const driverDetails = ref<any>(null);
  const isLoading = ref<boolean>(false);

  async function fetchZohoDetails() {
    const zohoId = user.value?.zoho_id;
    
    // We only guard against missing IDs or double-fetching while loading
    if (!zohoId || isLoading.value) return;

    isLoading.value = true;
    try {
      console.log('Fetching fresh data from Zoho for:', zohoId);
      
      const response = await get<any>(
        `http://localhost:8000/api/driver-details/${zohoId}`, 
        {}, 
        {
          'Accept': 'application/json',
          'Authorization': `Bearer ${authToken.value}`
        }
      );
      
      if (response && response.success) {
        driverDetails.value = response.data;
      }
    } catch (error) {
      console.error("Zoho Fetch Error:", error);
    } finally {
      isLoading.value = false;
    }
  }

    async function fetchDocuments() {
    const zohoId = user.value?.zoho_id;
    if (!zohoId) return;

    isDocsLoading.value = true;
    try {
        const response = await get<any>(
        `http://localhost:8000/api/driver-documents/${zohoId}`,
        {},
        { 'Authorization': `Bearer ${authToken.value}` }
        );
        
        if (response && response.data) {
        documents.value = response.data;
        }
    } catch (error) {
        console.error("Error fetching documents:", error);
    } finally {
        isDocsLoading.value = false;
    }
  }

  return {
    driverDetails: readonly(driverDetails),
    isLoading: readonly(isLoading),
    documents: readonly(documents),
    isDocsLoading: readonly(isDocsLoading),
    fetchZohoDetails,
    fetchDocuments,
    licenseNo: computed(() => driverDetails.value?.DL_Number || '---'),
    expiryDate: computed(() => driverDetails.value?.Expiry_Date || '---'),
    fullName: computed(() => driverDetails.value?.Full_Name || '---'),
    licenseExp: computed(() => driverDetails.value?.License_Exp || '---'),
    hiringStage: computed(() => driverDetails.value?.Hiring_Stage || '---'),
    carMakeModel: computed(() => `${driverDetails.value?.Make} ${driverDetails.value?.Model}` || '---'),
    insuranceExp: computed(() => driverDetails.value?.Insurance_Exp || '---'),
  };
}