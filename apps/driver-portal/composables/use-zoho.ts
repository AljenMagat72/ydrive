import { computed, readonly, ref } from 'vue';
import { useAuth, useAPI, useAuthToken } from '#imports';

interface ZohoDriver {
  Full_Name?: string;
  Phone?: string;
  Date_of_Birth?: string;
  Make?: string;
  Model?: string;
  Year?: string | number;
  Bank_Name?: string;
  Bank_Account?: string;
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

// Keep these outside the function if you want state to persist across components
const driverDetails = ref<ZohoDriver | null>(null);
const documents = ref<any[]>([]);
const isDocsLoading = ref<boolean>(false);
const isLoading = ref<boolean>(false);

export function useZoho() {
  const { user } = useAuth();
  const { get } = useAPI();
  const authToken = useAuthToken();

  async function fetchZohoDetails() {
    const zohoId = user.value?.zoho_id;
    if (!zohoId || isLoading.value) return;

    isLoading.value = true;
    try {
      const response = await get<any>(
        `/api/driver-details/${zohoId}`, 
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
    if (!zohoId || isDocsLoading.value) return;

    isDocsLoading.value = true;
    try {
      const response = await get<any>(
        `/api/driver-documents/${zohoId}`,
        {},
        { 'Authorization': `Bearer ${authToken.value}` }
      );
      if (response && response.data) documents.value = response.data;
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
    
    // --- Identification & Personal ---
    fullName: computed(() => driverDetails.value?.Full_Name || '---'),
    phone: computed(() => driverDetails.value?.Phone || '---'),
    dob: computed(() => driverDetails.value?.Date_of_Birth || '---'),

    // --- Vehicle Details ---
    make: computed(() => driverDetails.value?.Make || '---'),
    model: computed(() => driverDetails.value?.Model || '---'),
    year: computed(() => driverDetails.value?.Year || '---'),

    // --- Banking & Tax ---
    bankName: computed(() => driverDetails.value?.Bank_Name || '---'),
    bankAccount: computed(() => driverDetails.value?.Bank_Account || '---'),
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