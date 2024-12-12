import { MouseEventHandler } from "react";

interface ButtonProps {
  text: string;
  variant?: "primary" | "secondary";
  className?: string;
  onClick?: () => void;
}

export const Button = ({ text, variant, className, onClick }: ButtonProps) => {
  const color = variant === "primary"
    ? "bg-accent text-accentText hover:bg-secondaryBackground hover:text-primaryText active:bg-secondaryBackground active:text-primaryText"
    : "bg-secondaryBackground text-primaryText hover:bg-accent hover:text-accentText active:bg-accent active:text-accentText";

  return (
    <button
      onClick={onClick}
      type="button"
      className={`${color} hover:bg-accent hover:text-accentText px-4 py-2 font-medium rounded-lg inline-flex items-center justify-center align-center gap-2 ${className} `}
    >
      {text}
    </button>
  );
};