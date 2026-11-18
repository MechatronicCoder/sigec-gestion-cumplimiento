import { useEffect, useState } from "react";
import ReactECharts from "echarts-for-react";
import {
  Box,
  Typography,
  Select,
  MenuItem,
  Paper,
  Table,
  TableHead,
  TableBody,
  TableRow,
  TableCell,
  Chip,
  Alert,
  CircularProgress,
} from "@mui/material";
import { listEvaluaciones, getResumen } from "../api/checklist";
import DownloadIcon from "@mui/icons-material/Download";
import PictureAsPdfIcon from "@mui/icons-material/PictureAsPdf";
import Button from "@mui/material/Button";

const NIVEL_COLOR = {
  green: "#2e7d32",
  lime: "#9e9d24",
  amber: "#ed6c02",
  red: "#d32f2f",
  gray: "#9e9e9e",
};

function codigoDimension(nombre) {
  const partes = nombre.split(":");
  return partes.length > 1 ? partes[0].trim() : nombre;
}

export default function ReportesPage() {
  const [evaluaciones, setEvaluaciones] = useState([]);
  const [evaluacionId, setEvaluacionId] = useState("");
  const [resumen, setResumen] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    listEvaluaciones().then((evs) => {
      setEvaluaciones(evs);
      setEvaluacionId(evs[0]?.id || "");
    });
  }, []);

  useEffect(() => {
    if (!evaluacionId) {
      setLoading(false);
      return;
    }
    setLoading(true);
    getResumen(evaluacionId)
      .then(setResumen)
      .catch(() => setError("No se pudo cargar el resumen."))
      .finally(() => setLoading(false));
  }, [evaluacionId]);

  const gaugeOption = resumen && {
    series: [
      {
        type: "gauge",
        startAngle: 220,
        endAngle: -40,
        min: 0,
        max: 100,
        splitNumber: 5,
        radius: "85%",
        itemStyle: { color: NIVEL_COLOR[resumen.nivel.color] || "#9e9e9e" },
        progress: { show: true, width: 16 },
        axisLine: { lineStyle: { width: 16 } },
        axisTick: { show: false },
        splitLine: { length: 10, lineStyle: { width: 2 } },
        axisLabel: { distance: 16, fontSize: 10 },
        pointer: { show: false },
        detail: {
          valueAnimation: true,
          formatter: (v) => `${v}%`,
          fontSize: 22,
          fontWeight: 600,
          offsetCenter: [0, "5%"],
          color: "inherit",
        },
        data: [{ value: resumen.cumplimiento_global ?? 0 }],
      },
    ],
  };

  const barDimensionesOption = resumen && {
    tooltip: {
      trigger: "axis",
      formatter: (params) => {
        const p = params[0];
        const dim = resumen.dimensiones[p.dataIndex];
        return `${dim.nombre}<br/>Cumplimiento: ${p.value}%`;
      },
    },
    grid: { left: 50, right: 20, bottom: 30, top: 20 },
    xAxis: {
      type: "category",
      data: resumen.dimensiones.map((d) => codigoDimension(d.nombre)),
      axisLabel: { fontSize: 12, interval: 0 },
    },
    yAxis: { type: "value", max: 100 },
    series: [
      {
        type: "bar",
        data: resumen.dimensiones.map((d) => ({
          value: d.cumplimiento ?? 0,
          itemStyle: { color: "#1976d2" },
        })),
        barWidth: "50%",
      },
    ],
  };

  const barIndicadoresOption = resumen && {
    tooltip: { trigger: "axis" },
    grid: { left: 220, right: 40, bottom: 20, top: 20 },
    xAxis: { type: "value", max: 100 },
    yAxis: {
      type: "category",
      data: resumen.indicadores.map((i) => i.nombre),
      axisLabel: { fontSize: 10 },
      inverse: true,
    },
    series: [
      {
        type: "bar",
        data: resumen.indicadores.map((i) => ({
          value: i.cumplimiento ?? 0,
          itemStyle: { color: "#009688" },
        })),
        barWidth: "60%",
      },
    ],
  };

  return (
    <Box>
      <Typography variant="h4" gutterBottom>
        Reportes
      </Typography>

      {evaluaciones.length > 0 && (
      <Box sx={{ display: "flex", alignItems: "center", gap: 2, mb: 3, flexWrap: "wrap" }}>
        <Select
          size="small"
          value={evaluacionId}
          onChange={(e) => setEvaluacionId(e.target.value)}
          sx={{ minWidth: 260 }}
        >
          {evaluaciones.map((ev) => (
            <MenuItem key={ev.id} value={ev.id}>
              {ev.nombre}
            </MenuItem>
          ))}
        </Select>

        {evaluacionId && (
            <>
              <Button
                variant="outlined"
                size="small"
                startIcon={<DownloadIcon />}
                component="a"
                href={`/reportes/${evaluacionId}/exportar-excel`}
              >
                Excel
              </Button>
              <Button
                variant="outlined"
                size="small"
                color="error"
                startIcon={<PictureAsPdfIcon />}
                component="a"
                href={`/reportes/${evaluacionId}/exportar-pdf`}
              >
                PDF
              </Button>
            </>
          )}
        </Box>
      )}

      {error && <Alert severity="error">{error}</Alert>}

      {loading && (
        <Box sx={{ display: "flex", justifyContent: "center", mt: 4 }}>
          <CircularProgress />
        </Box>
      )}

      {!loading && resumen && (
        <>
          {/* Seccion 1: Cumplimiento global, sola */}
          <Paper sx={{ p: 3, mb: 4, textAlign: "center" }}>
            <Typography variant="h6" gutterBottom>
              Cumplimiento global
            </Typography>
            <ReactECharts option={gaugeOption} style={{ height: 260, maxWidth: 360, margin: "0 auto" }} />
            <Chip
              label={resumen.nivel.label}
              sx={{
                bgcolor: NIVEL_COLOR[resumen.nivel.color] || "#9e9e9e",
                color: "white",
                fontWeight: 600,
              }}
            />
          </Paper>

          {/* Seccion 2: Detalle por dimension, seguido de su grafico */}
          <Typography variant="h6" gutterBottom>
            Detalle por dimension
          </Typography>
          <Paper sx={{ mb: 2 }}>
            <Table size="small">
              <TableHead>
                <TableRow>
                  <TableCell>Dimension</TableCell>
                  <TableCell>Peso</TableCell>
                  <TableCell>Items calificados</TableCell>
                  <TableCell>Cumplimiento</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {resumen.dimensiones.map((d) => (
                  <TableRow key={d.nombre}>
                    <TableCell>{d.nombre}</TableCell>
                    <TableCell>{d.peso}%</TableCell>
                    <TableCell>
                      {d.calificados} / {d.total_items}
                    </TableCell>
                    <TableCell>
                      {d.cumplimiento !== null ? `${d.cumplimiento}%` : "-"}
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </Paper>
          <Typography variant="subtitle1" gutterBottom>
            Cumplimiento por dimension
          </Typography>
          <Paper sx={{ p: 2, mb: 4 }}>
            <ReactECharts option={barDimensionesOption} style={{ height: 280 }} />
          </Paper>

          {/* Seccion 3: Detalle por indicador, seguido de su grafico */}
          <Typography variant="h6" gutterBottom>
            Detalle por indicador
          </Typography>
          <Paper sx={{ mb: 2 }}>
            <Table size="small">
              <TableHead>
                <TableRow>
                  <TableCell>Dimension</TableCell>
                  <TableCell>Indicador</TableCell>
                  <TableCell>Items calificados</TableCell>
                  <TableCell>Cumplimiento</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {resumen.indicadores.map((i) => (
                  <TableRow key={i.dimension + i.nombre}>
                    <TableCell>{i.dimension}</TableCell>
                    <TableCell>{i.nombre}</TableCell>
                    <TableCell>
                      {i.calificados} / {i.total_items}
                    </TableCell>
                    <TableCell>
                      {i.cumplimiento !== null ? `${i.cumplimiento}%` : "-"}
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </Paper>
          <Typography variant="subtitle1" gutterBottom>
            Cumplimiento por indicador
          </Typography>
          <Paper sx={{ p: 2 }}>
            <ReactECharts
              option={barIndicadoresOption}
              style={{ height: Math.max(300, resumen.indicadores.length * 28) }}
            />
          </Paper>
        </>
      )}
    </Box>
  );
}
