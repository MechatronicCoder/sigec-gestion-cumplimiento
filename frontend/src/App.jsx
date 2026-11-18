import { Routes, Route } from "react-router-dom";
import { AuthProvider } from "./context/AuthContext";
import ProtectedRoute from "./components/ProtectedRoute";
import RoleRoute from "./components/RoleRoute";
import Layout from "./components/Layout";
import LoginPage from "./pages/LoginPage";
import DashboardPage from "./pages/DashboardPage";
import DimensionesPage from "./pages/DimensionesPage";
import IndicadoresPage from "./pages/IndicadoresPage";
import ItemsPage from "./pages/ItemsPage";
import ChecklistPage from "./pages/ChecklistPage";
import ReportesPage from "./pages/ReportesPage";
import UsuariosPage from "./pages/UsuariosPage";
import AuditoriaPage from "./pages/AuditoriaPage";

function App() {
  return (
    <AuthProvider>
      <Routes>
        <Route path="/login" element={<LoginPage />} />

        <Route element={<ProtectedRoute />}>
          <Route element={<Layout />}>
            <Route path="/" element={<DashboardPage />} />
            <Route path="/reportes" element={<ReportesPage />} />

            <Route element={<RoleRoute roles={["admin", "evaluador"]} />}>
              <Route path="/checklist" element={<ChecklistPage />} />
            </Route>

            <Route element={<RoleRoute roles={["admin"]} />}>
              <Route path="/admin/dimensiones" element={<DimensionesPage />} />
              <Route
                path="/admin/dimensiones/:dimensionId/indicadores"
                element={<IndicadoresPage />}
              />
              <Route
                path="/admin/indicadores/:indicadorId/items"
                element={<ItemsPage />}
              />
              <Route path="/admin/usuarios" element={<UsuariosPage />} />
              <Route path="/admin/auditoria" element={<AuditoriaPage />} />
            </Route>
          </Route>
        </Route>
      </Routes>
    </AuthProvider>
  );
}

export default App;
