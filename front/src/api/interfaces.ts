export interface RequestResult<T> {
  error: boolean,
  response: boolean,
  status: number,
  data: T | null
}

export interface User {
  name: string;
  email: string;
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
