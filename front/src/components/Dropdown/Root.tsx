import { Children, ReactNode, useRef, useState } from 'react'
import { Menu } from './Menu';
import { IoMdArrowDropdown } from "react-icons/io";

import { CloseDropdownContext } from './CloseDropdownContext';
import { useClickOutside } from '@/hooks/useClickOutside';

interface RootProps {
  title: string;
  children?: ReactNode;
}

export const Root = ({ title, children }: RootProps) => {
  const [isOpen, setIsOpen] = useState(false);
  const modalRef = useRef<HTMLDivElement | null>(null);

  const toggleDropdown = () => {
    setIsOpen(!isOpen);
  };

  const closeDropdown = () => {
    setIsOpen(false);
  };

  useClickOutside(modalRef, closeDropdown);

  return (
    <div ref={modalRef} className='py-6 pb-8'>
      <div className="relative inline-block">
        <button
          type="button"
          className="text-primaryText bg-secondaryBackground hover:bg-accent hover:text-accentText px-4 py-2 font-medium rounded-lg text-sm inline-flex items-center justify-center align-center gap-2"
          onClick={toggleDropdown}
        >
          {title}<IoMdArrowDropdown />
        </button>
        <Menu show={isOpen}>
          {Children.map(children, (child) => (
            <CloseDropdownContext.Provider value={{ closeDropdown }}>
              {child}
            </CloseDropdownContext.Provider>
          ))}
        </Menu>
      </div>
    </div>
  )
}
