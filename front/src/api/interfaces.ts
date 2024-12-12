export interface RequestResult<T> {
  error: boolean,
  response: boolean,
  status: number,
  data: T | null
}

export interface PostContract {
  simulated_datetime?: string;
  plan_id: number;
}

export interface PostSwitchContract {
  simulated_datetime?: string;
  plan_id: number;
}

export interface Contract {
  id: number;
  user_id: number;
  plan_id: number;
  active: boolean;
  createdAt: string;
  updatedAt: string;
}

export interface User {
  name: string;
  email: string;
  active_contract?: Contract
}

export interface Plan {
  id: number
  description: string
  number_of_clients: number
  gigabytes_storage: number
  price: string
  active: boolean
  created_at: string
  updated_at: string
}

export interface Payment {
  id: string
  contract_id: string
  plan_price: string
  discount: string
  amount_charged: string
  credit_remaining: string
  due_date: string
  status: string
  created_at: string
  updated_at: string
}
