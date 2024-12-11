// NOTE: Need import `React` to solve `React refers to a UMD global` error on ThemeContext.Provider JSX
import React, { useEffect, useState } from "react";
import { Theme, ThemeContext } from "../context";

const prefersDarkColorScheme = () =>
  window &&
  window.matchMedia &&
  window.matchMedia('(prefers-color-scheme: dark)').matches;

export const ThemeProvider = ({ children }: { children: React.ReactNode }) => {
  const [theme, setTheme] = useState<Theme | null>(null);
  const [themeClass, setThemeClass] = useState<string>("theme-light");

  const getThemeOrDefault = (): Theme => {
    if (theme) return theme;

    if (prefersDarkColorScheme()) return Theme.DARK;

    return Theme.LIGHT;
  }

  const loadTheme = () => {
    const currentTheme = localStorage.getItem("theme");

    if (currentTheme === 'dark') {
      setTheme(Theme.DARK);
      return;
    }

    if (currentTheme === 'light') {
      setTheme(Theme.LIGHT);
      return;
    }

    setTheme(Theme.DEFAULT);
  }

  useEffect(() => {
    loadTheme();
  }, []);

  useEffect(() => {
    if (theme)
      localStorage.setItem("theme", theme);

    if (theme === Theme.DARK) {
      setThemeClass('theme-dark');
      return;
    }

    if (theme === Theme.LIGHT) {
      setThemeClass('theme-light');
      return;
    }

    // user system theme
    if (prefersDarkColorScheme()) {
      setThemeClass('theme-dark');
      return
    }
    setThemeClass('theme-light');
  }, [theme]);

  useEffect(() => {
    if (!theme) return;
    document.body.className = themeClass;
  }, [themeClass]);

  return (
    <ThemeContext.Provider value={{ theme: theme ?? getThemeOrDefault(), setTheme }}>
      {children}
    </ThemeContext.Provider>
  );
};
