import { createContext } from "react";

export enum Theme {
  DEFAULT = "default",
  DARK = "dark",
  LIGHT = "light",
}

export interface ThemeContextInterface {
  theme: Theme;
  setTheme(thme: Theme): void;
}

export const ThemeContext = createContext<ThemeContextInterface | null>(null);