const timeSlots: Array<string> = [];
for (let i = 0; i < 24; i++) {
  const hour = i.toString().padStart(2, '0');
  timeSlots.push(`${hour}:00`);
}

const timeIncrements: Array<{ value: number, formatted: string }> = [];
for (let minutes = 0; minutes < 1440; minutes += 30) {
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;

  const hour12 = hours === 0 || hours === 24 ? 12 : hours > 12 ? hours - 12 : hours;
  const ampm = hours < 12 || hours === 24 ? 'AM' : 'PM';

  const formattedMins = mins.toString().padStart(2, '0');

  const timeString = `${hour12}:${formattedMins} ${ampm}`;
  timeIncrements.push({
    value: minutes,
    formatted: timeString
  });
}

export { timeSlots, timeIncrements };