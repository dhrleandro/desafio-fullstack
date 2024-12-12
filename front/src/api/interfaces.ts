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
