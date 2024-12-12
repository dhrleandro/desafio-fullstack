import { Container } from "@/components/Container";
import { useApiUser } from "@/hooks/useApiUser";

export const Profile = () => {
  const { isUserLoading, user } = useApiUser();

  return (
    <Container title="Meu Perfil" loading={isUserLoading} backTo="/">
      <p className="text-xl">
        <strong>Nome:</strong> {user?.name}
      </p>
      <p className="text-xl">
        <strong>Email:</strong> {user?.email}
      </p>
    </Container>
  )
}