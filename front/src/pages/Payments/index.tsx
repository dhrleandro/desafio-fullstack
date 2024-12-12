import { Container } from "@/components/Container";
import { useApiPayments } from "@/hooks/useApiPayments";
import { PaymentTable } from "./PaymentTable";

export const Payments = () => {
  const { loading, payments } = useApiPayments();

  return (
    <Container title="Meus Contratos" loading={loading} backTo="/">
      <div className="w-full mt-2">
        {payments && <PaymentTable payments={payments} />}
      </div>
    </Container>
  )
}
