import { Container } from "@/components/Container";
import { useApiContracts } from "@/hooks/useApiContracts";
import { ContractTable } from "./ContractTable";

export const Contracts = () => {
  const { loading, contracts } = useApiContracts();

  return (
    <Container title="Meus Contratos" loading={loading} backTo="/">
      <div className="w-full mt-2">
        {contracts && <ContractTable contracts={contracts} />}
      </div>
    </Container>
  )
}
