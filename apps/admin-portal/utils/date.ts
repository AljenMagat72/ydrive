export const getMondayOfCurrentWeek = () => {
  const today = new Date();
  const day = today.getDay(); // 0 = Sunday, 1 = Monday
  const diff = day === 0 ? -6 : 1 - day; // adjust when Sunday
  const monday = new Date(today);
  monday.setDate(today.getDate() + diff);

  // Format as YYYY-MM-DD in local time
  const yyyy = monday.getFullYear();
  const mm = String(monday.getMonth() + 1).padStart(2, "0");
  const dd = String(monday.getDate()).padStart(2, "0");

  return `${yyyy}-${mm}-${dd}`;
};

// CONSTANTS TIME SLOTS
export const scheduleTimeSlots = [
  { id: 1, time: "00:00", label: "12:00 AM" },
  { id: 2, time: "00:30", label: "12:30 AM" },
  { id: 3, time: "01:00", label: "1:00 AM" },
  { id: 4, time: "01:30", label: "1:30 AM" },
  { id: 5, time: "02:00", label: "2:00 AM" },
  { id: 6, time: "02:30", label: "2:30 AM" },
  { id: 7, time: "03:00", label: "3:00 AM" },
  { id: 8, time: "03:30", label: "3:30 AM" },
  { id: 9, time: "04:00", label: "4:00 AM" },
  { id: 10, time: "04:30", label: "4:30 AM" },
  { id: 11, time: "05:00", label: "5:00 AM" },
  { id: 12, time: "05:30", label: "5:30 AM" },
  { id: 13, time: "06:00", label: "6:00 AM" },
  { id: 14, time: "06:30", label: "6:30 AM" },
  { id: 15, time: "07:00", label: "7:00 AM" },
  { id: 16, time: "07:30", label: "7:30 AM" },
  { id: 17, time: "08:00", label: "8:00 AM" },
  { id: 18, time: "08:30", label: "8:30 AM" },
  { id: 19, time: "09:00", label: "9:00 AM" },
  { id: 20, time: "09:30", label: "9:30 AM" },
  { id: 21, time: "10:00", label: "10:00 AM" },
  { id: 22, time: "10:30", label: "10:30 AM" },
  { id: 23, time: "11:00", label: "11:00 AM" },
  { id: 24, time: "11:30", label: "11:30 AM" },
  { id: 25, time: "12:00", label: "12:00 PM" },
  { id: 26, time: "12:30", label: "12:30 PM" },
  { id: 27, time: "13:00", label: "1:00 PM" },
  { id: 28, time: "13:30", label: "1:30 PM" },
  { id: 29, time: "14:00", label: "2:00 PM" },
  { id: 30, time: "14:30", label: "2:30 PM" },
  { id: 31, time: "15:00", label: "3:00 PM" },
  { id: 32, time: "15:30", label: "3:30 PM" },
  { id: 33, time: "16:00", label: "4:00 PM" },
  { id: 34, time: "16:30", label: "4:30 PM" },
  { id: 35, time: "17:00", label: "5:00 PM" },
  { id: 36, time: "17:30", label: "5:30 PM" },
  { id: 37, time: "18:00", label: "6:00 PM" },
  { id: 38, time: "18:30", label: "6:30 PM" },
  { id: 39, time: "19:00", label: "7:00 PM" },
  { id: 40, time: "19:30", label: "7:30 PM" },
  { id: 41, time: "20:00", label: "8:00 PM" },
  { id: 42, time: "20:30", label: "8:30 PM" },
  { id: 43, time: "21:00", label: "9:00 PM" },
  { id: 44, time: "21:30", label: "9:30 PM" },
  { id: 45, time: "22:00", label: "10:00 PM" },
  { id: 46, time: "22:30", label: "10:30 PM" },
  { id: 47, time: "23:00", label: "11:00 PM" },
  { id: 48, time: "23:30", label: "11:30 PM" },
  { id: 49, time: "00:00", label: "12:00 AM" },
];

export const daysOfTheWeek = [
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
  "Sunday",
];
