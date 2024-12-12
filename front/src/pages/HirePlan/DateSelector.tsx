import { useEffect, useState } from "react";

export const DateSelector = ({ onChange }: { onChange?: (date: string) => void }) => {
  const [value, setValue] = useState(new Date().toISOString().slice(0, 10));

  useEffect(() => {
    onChange?.(value);
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