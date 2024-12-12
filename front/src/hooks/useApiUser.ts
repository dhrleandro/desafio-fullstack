import { fetchData } from "@/api/api";
import { User } from "@/api/interfaces";
import { useEffect, useState } from "react";

export const useApiUser = () => {
  const [user, setUser] = useState<User | null>(null)
  const [isUserLoading, setIsUserLoading] = useState<boolean>(true)

  useEffect(() => {
    const request = async () => {
      const response = await fetchData<User>('/user');
      if (response.error) return;
      setUser(response.data);
    }

    request();
  }, []);

  useEffect(() => {
    if (user) setIsUserLoading(false);
  }, [user]);

  return { isUserLoading, user }
}