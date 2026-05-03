import './bootstrap';
import '../sass/app.scss';
import 'bootstrap-icons/font/bootstrap-icons.css';

// Bootstrap 5（JS bundle に Popper を含む）
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Chart.js（Phase 10 ダッシュボードや投稿後分析で利用予定）
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);
window.Chart = Chart;
