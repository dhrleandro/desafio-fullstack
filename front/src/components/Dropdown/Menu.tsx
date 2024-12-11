import { ReactNode } from "react";

interface MenuProps {
  show: boolean;
  children?: ReactNode;
}

export const Menu = ({ show, children }: MenuProps) => {
  if (!show) return;

  return (
    <div className="origin-top-right absolute right-0 mt-2 w-44 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5">
      <ul role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
        {children}
      </ul>
    </div>
  )
}