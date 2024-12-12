import { fetchData } from "@/api/api";
import { Contract } from "@/api/interfaces";
import { useEffect, useState } from "react";

export const useApiContracts = () => {
  const [contracts, setContracts] = useState<Contract[] | null>(null)
  const [loading, setLoading] = useState<boolean>(true)

  useEffect(() => {
    const request = async () => {
      const response = await fetchData<Contract[]>('/contracts');
      if (response.error) return;
      setContracts(response.data);
    }

    request();
  }, []);

  useEffect(() => {
    if (contracts) setLoading(false);
  }, [contracts]);

  return { loading, contracts }
}