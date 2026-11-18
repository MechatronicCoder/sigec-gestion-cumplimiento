import { Navigate, Outlet } from "react-router-dom";
import { useAuth } from "../context/AuthContext";

/**
 * Uso: <Route element={<RoleRoute roles={['admin','evaluador']} />}>...</Route>
 * Si el usuario no tiene ninguno de los roles permitidos, lo manda al Dashboard.
 */
export default function RoleRoute({ roles }) {
  const { user } = useAuth();
  const misRoles = (user?.roles || []).map((r) => r.name);
  const permitido = roles.some((r) => misRoles.includes(r));

  if (!permitido) {
    return <Navigate to="/" replace />;
  }

  return <Outlet />;
}
