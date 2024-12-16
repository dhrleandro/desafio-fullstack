const numberToStringTwoDigits = (num: number): string => {
  if (num < 10) return `0${num}`;
  return num.toString();
}

const localDateTimeToString = (date: Date, time: boolean = true): string => {
  const month = numberToStringTwoDigits(date.getMonth() + 1);
  const day = numberToStringTwoDigits(date.getDate());
  const hours = numberToStringTwoDigits(date.getHours());
  const minutes = numberToStringTwoDigits(date.getMinutes());
  const seconds = numberToStringTwoDigits(date.getSeconds());

  if (!time) return `${date.getFullYear()}-${month}-${day}`;

  return `${date.getFullYear()}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

const UTCDateTimeToString = (date: Date, time: boolean = true): string => {
  const iso = date.toISOString();

  if (!time) return iso.slice(0, 10);

  return iso.slice(0, 19).replace('T', ' ');
}

const localDateAsIfItWereUTC = (fullYear: number, month: number, day: number): Date => {
  const iso = `${fullYear}-${numberToStringTwoDigits(month)}-${numberToStringTwoDigits(day)}T00:00:00Z`;
  console.log(iso);
  const utcDate = new Date(iso);

  return utcDate;
}

const datetime = {
  localDateTimeToString,
  UTCDateTimeToString,
  localDateAsIfItWereUTC
}

export default datetime;