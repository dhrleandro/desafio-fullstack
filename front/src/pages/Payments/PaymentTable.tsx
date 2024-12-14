import { Payment } from "@/api/interfaces";

/**
 * https://stackoverflow.com/questions/673905/how-can-i-determine-a-users-locale-within-the-browser/78786023#78786023
 */
export function determineLocale(): string {
  // All modern browsers support this. Should match what's used by localeCompare() etc.
  const intl = window.Intl;
  if (intl !== undefined) {
      return intl.NumberFormat().resolvedOptions().locale;
  }

  // Fall back to ranked choice locales, which are configured in the browser but aren't necessarily
  // what's used in functions like localeCompare().
  const languages = navigator.languages as (string[] | undefined);
  if (languages !== undefined && languages.length > 0) {
      return languages[0];
  }

  // Old standard.
  return navigator.language ?? "en-US";
}

/**
 * Returns UTC date string in the user's locale.
 * 
 * The due date is always midnight. Since UTC ranges from -12 to +14 (less than 24 hours),
 * we can display the date in UTC with the user's local formatting.
 */
const formatPaymentDate = (str: string): string => {
  const date = new Date(str);
  return date.toLocaleDateString(determineLocale(), { timeZone: 'UTC' });
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
  if (!payments || payments.length === 0) {
    return <h1 className="text-primaryText text-lg font-normal">Nenhum pagamento encontrado.</h1>
  }

  return (
    <div className="overflow-x-auto shadow-md">
      <table className="w-full bg-secondaryBackground border border-accent">
        <thead>
          <tr className="bg-accent border-b border-accent shadow-md">
            <th className="px-6 py-3 text-left text-sm font-bold text-accentText">Num. do Contrato</th>
            <th className="min-w-[8rem] px-6 py-3 text-left text-sm font-bold text-accentText">Valor do Plano</th>
            <th className="min-w-[8rem] px-6 py-3 text-left text-sm font-bold text-accentText">Desconto</th>
            <th className="min-w-[8rem] px-6 py-3 text-left text-sm font-bold text-accentText">Valor Cobrado</th>
            <th className="min-w-[8rem] px-6 py-3 text-left text-sm font-bold text-accentText">Crédito Remanescente</th>
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
