import { useEffect, useState } from "react";
import datetime from "@/lib/datetime";

export const DateSelector = ({ onChange }: { onChange?: (date: Date) => void }) => {
  const [value, setValue] = useState(datetime.localDateTimeToString(new Date(), false));

  useEffect(() => {
    if (!value) return;

    try {
      const split = value.split('-');
      if (split.length !== 3) throw new Error('Invalid date format');

      const fullYear = parseInt(split[0]);
      const month = parseInt(split[1]);
      const day = parseInt(split[2]);
      const date = datetime.localDateAsIfItWereUTC(fullYear, month, day);

      console.log({value, date: date.toLocaleString(), utc1: date.toISOString(), utc: datetime.UTCDateTimeToString(date, false)});
      onChange?.(date);
    } catch (error) {
      console.error(error);
    }
  }, [value]);

  return (
    <div className="w-full flex flex-col gap-2 items-center justify-center mb-2">
       <p>Simule a data de contratação:</p>
        <input
          className="block text-primaryText bg-secondaryBackground border border-accent rounded-lg text-sm focus:ring-accent focus:border-accent p-2.5"
          type="date"
          value={value}
          onChange={(e) => setValue(e.target.value)}
        />
    </div>
  )
}