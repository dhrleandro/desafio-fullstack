import { Link } from "react-router-dom"

export const NavItem = ({ to, text }: { to?: string, text: string }) => {
  return (
    <Link to={to ?? '#'} className="h-full px-2 hover:bg-accent hover:text-accentText flex flex-row items-center justify-between cursor-pointer">
        {text}
    </Link>
  )
}