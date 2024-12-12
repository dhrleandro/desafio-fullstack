export const NavItem = ({ text }: { text: string }) => {
  return (
    <div className="h-full px-2 hover:bg-accent hover:text-accentText flex flex-row items-center justify-between cursor-pointer">
      <a href="#" className="">{text}</a>
    </div>
  )
}