import { fetchData } from "@/api/api";
import { Payment } from "@/api/interfaces";
import { useEffect, useState } from "react";

export const useApiPayments = () => {
  const [payments, setPayments] = useState<Payment[] | null>(null)
  const [loading, setLoading] = useState<boolean>(true)

  useEffect(() => {
    const request = async () => {
      const response = await fetchData<Payment[]>('/payments');
      if (response.error) return;
      setPayments(response.data);
    }

    request();
  }, []);

  useEffect(() => {
    if (payments) setLoading(false);
  }, [payments]);

  return { loading, payments }
}