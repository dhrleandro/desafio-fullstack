import { fetchData } from "@/api/api";
import { Plan } from "@/api/interfaces";
import { useEffect, useState } from "react";

export const useApiPlans = () => {
  const [plans, setPlans] = useState<Plan[] | null>(null)
  const [loading, setLoading] = useState<boolean>(true)

  useEffect(() => {
    const request = async () => {
      const response = await fetchData<Plan[]>('/plans');
      if (response.error) return;
      setPlans(response.data);
    }

    request();
  }, []);

  useEffect(() => {
    if (plans) setLoading(false);
  }, [plans]);

  return { loading, plans }
}