import { Payment } from "@/api/interfaces";

const formatPaymentDate = (str: string): string => {
  const date = new Date(str);
  return date.toLocaleDateString();
}

const formatStatus = (status: string): string => {
  switch (status) {
    case 'pending':
      return 'Pendente';
    case 'confirmed':
      return 'Confirmado';
    case 'canceled':
      return 'Cancelado';
  }

  return '';
}

export const PaymentTable = ({ payments }: { payments: Payment[] }) => {
  return (
    <div className="overflow-x-auto shadow-md">
      <table className="w-full bg-secondaryBackground border border-accent">
        <thead>
          <tr className="bg-accent border-b border-accent shadow-md">
            <th className="px-6 py-3 text-left text-sm font-bold text-accentText">ID Contrato</th>
            <th className="px-6 py-3 text-left text-sm font-bold text-accentText">Valor Plano</th>
            <th className="px-6 py-3 text-left text-sm font-bold text-accentText">Disconto</th>
            <th className="px-6 py-3 text-left text-sm font-bold text-accentText">Valor Cobrado</th>
            <th className="px-6 py-3 text-left text-sm font-bold text-accentText">Crédito Remanescente</th>
            <th className="px-6 py-3 text-left text-sm font-bold text-accentText">Status</th>
            <th className="px-6 py-3 text-left text-sm font-bold text-accentText">Data de Vencimento</th>
          </tr>
        </thead>
        <tbody>
          {payments.map((payment) => (
            <tr key={payment.id} className="border-b border-accent">
              <td className="px-6 py-4 text-sm text-primaryText">{payment.contract_id}</td>
              <td className="px-6 py-4 text-sm text-primaryText">R$ {payment.plan_price.replace(".", ",")}</td>
              <td className="px-6 py-4 text-sm text-primaryText">R$ {payment.discount.replace(".", ",")}</td>
              <td className="px-6 py-4 text-sm text-primaryText">R$ {payment.amount_charged.replace(".", ",")}</td>
              <td className="px-6 py-4 text-sm text-primaryText">R$ {payment.credit_remaining.replace(".", ",")}</td>
              <td className="px-6 py-4 text-sm text-primaryText">{formatStatus(payment.status)}</td>
              <td className="px-6 py-4 text-sm text-primaryText">{formatPaymentDate(payment.due_date)}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};
