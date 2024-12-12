import { ThemeSwitch } from "@/components/ThemeSwitch"
import { NavItem } from "./NavItem"
import Favicon from "@/assets/favicon.png"

export const Navbar = ({ className }: { className?: string }) => {
  return (
    <div className={`bg-secondaryBackground shadow-md py-2 md:py-0 px-2 w-full min-h-[60px] md:h-[48px] flex flex-col md:flex-row items-center justify-between ${className}`}>

      <div className="h-full flex flex-row items-center justify-between gap-2 text-primaryText">
        <img src={Favicon} className="w-[32px] h-[32px]"></img>
        <h1 className="text-primaryText text-lg font-bold">Desafio InMediam</h1>
      </div>

      <div className="flex h-full flex-col md:flex-row items-center justify-between gap-4 text-primaryText">
        <NavItem to="/" text="Início"/>
        <NavItem to="/contracts" text="Meus Contratos"/>
        <NavItem to="/payments" text="Meus Pagamentos"/>
        <NavItem to="/profile" text="Meu Perfil"/>
        <ThemeSwitch/>
      </div>

    </div>
  )
}
