import { Box, Typography, Paper } from "@mui/material";
import ConstructionIcon from "@mui/icons-material/Construction";

export default function PlaceholderPage({ title }) {
  return (
    <Paper sx={{ p: 4, textAlign: "center" }}>
      <ConstructionIcon sx={{ fontSize: 48, color: "text.secondary", mb: 2 }} />
      <Typography variant="h5" gutterBottom>
        {title}
      </Typography>
      <Typography color="text.secondary">
        Esta seccion todavia no esta conectada a la API. Aqui ira el CRUD
        correspondiente.
      </Typography>
    </Paper>
  );
}
