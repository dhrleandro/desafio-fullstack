import { Contract } from "@/api/interfaces";

const formatContractDate = (str: string): string => {
  const date = new Date(str);
  return `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
}

export const ContractTable = ({ contracts }: { contracts: Contract[] }) => {
  return (
    <div className="overflow-x-auto shadow-md">
      <table className="w-full bg-secondaryBackground border border-accent">
        <thead>
          <tr className="bg-accent border-b border-accent shadow-md">
            <th className="px-6 py-3 text-left text-sm font-bold text-accentText">ID</th>
            <th className="px-6 py-3 text-left text-sm font-bold text-accentText">Plano</th>
            <th className="px-6 py-3 text-left text-sm font-bold text-accentText">Ativo</th>
            <th className="px-6 py-3 text-left text-sm font-bold text-accentText">Data</th>
          </tr>
        </thead>
        <tbody>
          {contracts.map((contract) => (
            <tr key={contract.id} className="border-b border-accent">
              <td className="px-6 py-4 text-sm text-primaryText">{contract.id}</td>
              <td className="px-6 py-4 text-sm text-primaryText">{contract.plan_id} - {contract.plan?.description}</td>
              <td className="px-6 py-4 text-sm text-primaryText">{contract.active ? "Sim" : "Não"}</td>
              <td className="px-6 py-4 text-sm text-primaryText">{formatContractDate(contract.created_at)}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};
