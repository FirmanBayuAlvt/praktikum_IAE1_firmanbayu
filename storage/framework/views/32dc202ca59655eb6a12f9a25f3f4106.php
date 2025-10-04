<?php

    $chartData = $chartData ?? [];
    $meta = $meta ?? [];


    $c = is_countable($chartData) ? count($chartData) : 0;

    $last = $c >= 1 ? $chartData[$c - 1] : null;
    $prev = $c >= 2 ? $chartData[$c - 2] : null;

    $change = $last && $prev ? $last['close'] - $prev['close'] : 0;
    $changePct = $last && $prev && $prev['close'] ? ($change / $prev['close']) * 100 : 0;


    $labels = $c ? array_column($chartData, 'time') : [];
    $closeData = $c ? array_column($chartData, 'close') : [];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Saham <?php echo e($meta['2. Symbol'] ?? ''); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
        }
        .hero {
            background: linear-gradient(90deg, #0d6efd 60%, #198754 100%);
            color: #fff;
            padding: 40px 0 30px 0;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 4px 16px rgba(13,110,253,0.08);
            margin-bottom: 30px;
        }
        .card-summary {
            transition: transform 0.2s;
        }
        .card-summary:hover {
            transform: scale(1.04);
            box-shadow: 0 8px 24px rgba(13,110,253,0.12);
        }
        .table thead th {
            background: #0d6efd;
            color: #fff;
            font-weight: 600;
        }
        .table-success th {
            background: #198754 !important;
            color: #fff !important;
        }
        .table-warning th {
            background: #ffc107 !important;
            color: #212529 !important;
        }
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 18px;
            color: #0d6efd;
        }
        .card {
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        }
    </style>
</head>

<body>
    <div class="hero text-center">
        <h1 class="display-5 fw-bold mb-2"><i class="fa-solid fa-chart-line"></i> Dashboard Saham <?php echo e($meta['2. Symbol'] ?? ''); ?></h1>
        <p class="lead mb-0">Data real-time, harian, dan adjusted dari Alpha Vantage API</p>
        <small>Last Refreshed: <?php echo e($meta['3. Last Refreshed'] ?? '-'); ?> | Interval: <?php echo e($meta['4. Interval'] ?? '-'); ?></small>
    </div>
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex flex-wrap gap-3 justify-content-center mb-4">
                    <div class="card card-summary shadow-sm" style="min-width:180px;">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-1"><i class="fa-solid fa-dollar-sign"></i> Harga Terakhir</h6>
                            <h3 class="fw-bold mb-0 text-primary"><?php echo e($last ? number_format($last['close'], 2) : '-'); ?></h3>
                        </div>
                    </div>
                    <div class="card card-summary shadow-sm" style="min-width:180px;">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-1"><i class="fa-solid fa-arrow-trend-up"></i> Perubahan</h6>
                            <h3 class="fw-bold mb-0"
                                style="color:<?php echo e($change > 0 ? '#198754' : ($change < 0 ? '#dc3545' : '#6c757d')); ?>;">
                                <?php echo e($change > 0 ? '+' : ''); ?><?php echo e(number_format($change, 2)); ?>

                            </h3>
                            <span class="small"
                                style="color:<?php echo e($changePct > 0 ? '#198754' : ($changePct < 0 ? '#dc3545' : '#6c757d')); ?>;">
                                <?php echo e($changePct > 0 ? '+' : ''); ?><?php echo e(number_format($changePct, 2)); ?>%
                            </span>
                        </div>
                    </div>
                    <div class="card card-summary shadow-sm" style="min-width:180px;">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-1"><i class="fa-solid fa-coins"></i> Volume</h6>
                            <h3 class="fw-bold mb-0 text-success"><?php echo e($last ? number_format($last['volume']) : '-'); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if($c == 0): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">Data chart kosong — tidak ada data untuk ditampilkan.</div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <span class="section-title"><i class="fa-solid fa-clock"></i> Data Saham Real-Time (Intraday)</span>
                        <small><?php echo e($meta['3. Last Refreshed'] ?? ''); ?> | Interval: <?php echo e($meta['4. Interval'] ?? ''); ?></small>
                    </div>
                    <div class="card-body">
                        <canvas id="stockChart" height="100"></canvas>
                        <hr>
                        <h5 class="section-title"><i class="fa-solid fa-table"></i> Detail Data Intraday</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Open</th>
                                        <th>High</th>
                                        <th>Low</th>
                                        <th>Close</th>
                                        <th>Volume</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $chartData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php $isUp = $row['close'] >= $row['open']; ?>
                                        <tr style="background:<?php echo e($isUp ? '#e9fbe9' : '#fbe9e9'); ?>;">
                                            <td><?php echo e($row['time'] ?? '-'); ?></td>
                                            <td><?php echo e(isset($row['open']) ? number_format($row['open'], 2) : '-'); ?></td>
                                            <td><?php echo e(isset($row['high']) ? number_format($row['high'], 2) : '-'); ?></td>
                                            <td><?php echo e(isset($row['low']) ? number_format($row['low'], 2) : '-'); ?></td>
                                            <td style="color:<?php echo e($isUp ? '#198754' : '#dc3545'); ?>; font-weight:bold;">
                                                <?php echo e(isset($row['close']) ? number_format($row['close'], 2) : '-'); ?></td>
                                            <td><?php echo e(isset($row['volume']) ? number_format($row['volume']) : '-'); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <span class="section-title"><i class="fa-solid fa-calendar-day"></i> Data Saham Harian (Daily)</span>
                    <small><?php echo e($dailyMeta['3. Last Refreshed'] ?? ''); ?></small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-success">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Open</th>
                                    <th>High</th>
                                    <th>Low</th>
                                    <th>Close</th>
                                    <th>Volume</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $dailyChartData ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php $isUp = $row['close'] >= $row['open']; ?>
                                    <tr style="background:<?php echo e($isUp ? '#e9fbe9' : '#fbe9e9'); ?>;">
                                        <td><?php echo e($row['date'] ?? '-'); ?></td>
                                        <td><?php echo e(isset($row['open']) ? number_format($row['open'], 2) : '-'); ?></td>
                                        <td><?php echo e(isset($row['high']) ? number_format($row['high'], 2) : '-'); ?></td>
                                        <td><?php echo e(isset($row['low']) ? number_format($row['low'], 2) : '-'); ?></td>
                                        <td style="color:<?php echo e($isUp ? '#198754' : '#dc3545'); ?>; font-weight:bold;">
                                            <?php echo e(isset($row['close']) ? number_format($row['close'], 2) : '-'); ?></td>
                                        <td><?php echo e(isset($row['volume']) ? number_format($row['volume']) : '-'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <span class="section-title"><i class="fa-solid fa-calendar-days"></i> Data Saham Harian Adjusted (Daily Adjusted)</span>
                    <small><?php echo e($adjustedMeta['3. Last Refreshed'] ?? ''); ?></small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-warning">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Open</th>
                                    <th>High</th>
                                    <th>Low</th>
                                    <th>Close</th>
                                    <th>Adjusted Close</th>
                                    <th>Volume</th>
                                    <th>Dividend</th>
                                    <th>Split</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $adjustedChartData ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php $isUp = $row['close'] >= $row['open']; ?>
                                    <tr style="background:<?php echo e($isUp ? '#fffbe9' : '#fbe9e9'); ?>;">
                                        <td><?php echo e($row['date'] ?? '-'); ?></td>
                                        <td><?php echo e(isset($row['open']) ? number_format($row['open'], 2) : '-'); ?></td>
                                        <td><?php echo e(isset($row['high']) ? number_format($row['high'], 2) : '-'); ?></td>
                                        <td><?php echo e(isset($row['low']) ? number_format($row['low'], 2) : '-'); ?></td>
                                        <td style="color:<?php echo e($isUp ? '#198754' : '#dc3545'); ?>; font-weight:bold;">
                                            <?php echo e(isset($row['close']) ? number_format($row['close'], 2) : '-'); ?></td>
                                        <td><?php echo e(isset($row['adjusted_close']) ? number_format($row['adjusted_close'], 2) : '-'); ?></td>
                                        <td><?php echo e(isset($row['volume']) ? number_format($row['volume']) : '-'); ?></td>
                                        <td><?php echo e(isset($row['dividend']) ? number_format($row['dividend'], 4) : '-'); ?></td>
                                        <td><?php echo e(isset($row['split']) ? number_format($row['split'], 2) : '-'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">Tidak ada data</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const labels = <?php echo json_encode($labels, 15, 512) ?>;
        const closeData = <?php echo json_encode($closeData, 15, 512) ?>;
        const ctx = document.getElementById('stockChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Harga Penutupan',
                    data: closeData,
                    borderColor: 'rgba(13,110,253,1)',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    fill: true,
                    tension: 0.2,
                    pointRadius: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: 'Grafik Harga Penutupan Saham'
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Waktu'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Harga'
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>
<?php /**PATH D:\Integrasi Aplikasi Enterprise\IAE_1_FirmanBayuA\resources\views/external.blade.php ENDPATH**/ ?>