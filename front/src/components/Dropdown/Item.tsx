import { useContext } from "react";
import { CloseDropdownContext, CloseDropdownContextInterface } from "./CloseDropdownContext";

interface ItemProps {
  onClick?: () => void;
  selected?: boolean;
  children?: React.ReactNode;
}

export const Item = ({ onClick, selected, children }: ItemProps) => {
  const { closeDropdown } = useContext(CloseDropdownContext) as CloseDropdownContextInterface;

  const handleClick = () => {
    onClick?.();
    closeDropdown();
  }

  return (
    <li>
      <a
        href="#"
        className={`block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 ${selected ? 'bg-gray-300' : ''}`}
        onClick={handleClick}
      >
        {children}
      </a>
    </li>
  )
}