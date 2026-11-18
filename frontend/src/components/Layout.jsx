import { useState } from "react";
import { Outlet, Link as RouterLink, useLocation } from "react-router-dom";
import {
  AppBar,
  Toolbar,
  Typography,
  Drawer,
  List,
  ListItemButton,
  ListItemIcon,
  ListItemText,
  IconButton,
  Box,
  Divider,
  Avatar,
  Menu,
  MenuItem,
} from "@mui/material";
import MenuIcon from "@mui/icons-material/Menu";
import DashboardIcon from "@mui/icons-material/Dashboard";
import ChecklistIcon from "@mui/icons-material/FactCheck";
import AssessmentIcon from "@mui/icons-material/Assessment";
import LayersIcon from "@mui/icons-material/Layers";
import PeopleIcon from "@mui/icons-material/People";
import HistoryIcon from "@mui/icons-material/History";
import { useAuth } from "../context/AuthContext";

const drawerWidth = 240;

// roles: undefined = visible para cualquiera autenticado
const navItems = [
  { label: "Dashboard", path: "/", icon: <DashboardIcon /> },
  { label: "Checklist", path: "/checklist", icon: <ChecklistIcon />, roles: ["admin", "evaluador"] },
  { label: "Reportes", path: "/reportes", icon: <AssessmentIcon /> },
  { label: "Dimensiones", path: "/admin/dimensiones", icon: <LayersIcon />, roles: ["admin"] },
  { label: "Usuarios", path: "/admin/usuarios", icon: <PeopleIcon />, roles: ["admin"] },
  { label: "Auditoria", path: "/admin/auditoria", icon: <HistoryIcon />, roles: ["admin"] },
];

export default function Layout() {
  const [mobileOpen, setMobileOpen] = useState(false);
  const [anchorEl, setAnchorEl] = useState(null);
  const { user, logout } = useAuth();
  const location = useLocation();

  const misRoles = (user?.roles || []).map((r) => r.name);
  const puedeVer = (item) => !item.roles || item.roles.some((r) => misRoles.includes(r));
  const itemsVisibles = navItems.filter(puedeVer);

  const drawer = (
    <div>
      <Toolbar>
        <Typography variant="h6" noWrap>
          SIGEC v2.0
        </Typography>
      </Toolbar>
      <Divider />
      <List>
        {itemsVisibles.map((item) => (
          <ListItemButton
            key={item.path}
            component={RouterLink}
            to={item.path}
            selected={location.pathname === item.path}
          >
            <ListItemIcon>{item.icon}</ListItemIcon>
            <ListItemText primary={item.label} />
          </ListItemButton>
        ))}
      </List>
    </div>
  );

  return (
    <Box sx={{ display: "flex" }}>
      <AppBar
        position="fixed"
        sx={{
          width: { sm: `calc(100% - ${drawerWidth}px)` },
          ml: { sm: `${drawerWidth}px` },
        }}
      >
        <Toolbar sx={{ justifyContent: "space-between" }}>
          <IconButton
            color="inherit"
            edge="start"
            onClick={() => setMobileOpen(!mobileOpen)}
            sx={{ mr: 2, display: { sm: "none" } }}
          >
            <MenuIcon />
          </IconButton>

          <Typography variant="h6" noWrap component="div">
            Sistema de Gestion de Cumplimiento
          </Typography>

          <Box>
            <IconButton onClick={(e) => setAnchorEl(e.currentTarget)} sx={{ p: 0 }}>
              <Avatar sx={{ bgcolor: "secondary.main" }}>
                {user?.name?.[0]?.toUpperCase() || "?"}
              </Avatar>
            </IconButton>
            <Menu
              anchorEl={anchorEl}
              open={Boolean(anchorEl)}
              onClose={() => setAnchorEl(null)}
            >
              <MenuItem disabled>{user?.email}</MenuItem>
              <Divider />
              <MenuItem onClick={logout}>Cerrar sesion</MenuItem>
            </Menu>
          </Box>
        </Toolbar>
      </AppBar>

      <Box
        component="nav"
        sx={{ width: { sm: drawerWidth }, flexShrink: { sm: 0 } }}
      >
        <Drawer
          variant="temporary"
          open={mobileOpen}
          onClose={() => setMobileOpen(false)}
          ModalProps={{ keepMounted: true }}
          sx={{
            display: { xs: "block", sm: "none" },
            "& .MuiDrawer-paper": { width: drawerWidth },
          }}
        >
          {drawer}
        </Drawer>

        <Drawer
          variant="permanent"
          sx={{
            display: { xs: "none", sm: "block" },
            "& .MuiDrawer-paper": { width: drawerWidth, boxSizing: "border-box" },
          }}
          open
        >
          {drawer}
        </Drawer>
      </Box>

      <Box
        component="main"
        sx={{
          flexGrow: 1,
          p: 3,
          width: { sm: `calc(100% - ${drawerWidth}px)` },
        }}
      >
        <Toolbar />
        <Outlet />
      </Box>
    </Box>
  );
}
