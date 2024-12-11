import { createContext } from "react";

export interface CloseDropdownContextInterface {
  closeDropdown: () => void
}

export const CloseDropdownContext = createContext<CloseDropdownContextInterface | null>(null);