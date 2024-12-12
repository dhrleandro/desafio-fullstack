import { useContext } from "react";
import { Theme, ThemeContext, ThemeContextInterface } from "@/context";
import Dropdown from "@/components/Dropdown";
import { IoIosSettings, IoIosSunny, IoIosMoon } from "react-icons/io";

export const ThemeSwitch = () => {
  const { theme, setTheme } = useContext(ThemeContext) as ThemeContextInterface;

  return (
    <Dropdown.Root title="Tema">
      <Dropdown.Item onClick={() => setTheme(Theme.DEFAULT)} selected={theme === Theme.DEFAULT}>
          <IoIosSettings className="inline mr-1 align-middle" />
          <p className="inline align-middle">Padrão</p>
      </Dropdown.Item>
      <Dropdown.Item onClick={() => setTheme(Theme.LIGHT)}selected={theme === Theme.LIGHT}>
          <IoIosSunny className="inline mr-1" />
          <p className="inline align-middle">Claro</p>
      </Dropdown.Item>
      <Dropdown.Item onClick={() => setTheme(Theme.DARK)}selected={theme === Theme.DARK}>
        <IoIosMoon className="inline  mr-1" />
        <p className="inline align-middle">Escuro</p>
      </Dropdown.Item>
    </Dropdown.Root>
  )
}