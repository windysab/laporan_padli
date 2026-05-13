# Product Overview

**SI LAPER** (Sistem Laporan Perkara) — a court case reporting and monitoring system for **Pengadilan Agama Amuntai** (PA Amuntai), a religious court in South Kalimantan, Indonesia.

## Purpose

Internal web application for court staff to generate reports, monitor case progress, and visualize statistics from the SIPP (Sistem Informasi Penelusuran Perkara) database.

## Key Modules

- **Dashboard** — Daily/monthly/yearly case statistics, performance metrics (kinerja PN), and chart visualizations
- **Data Perkara Gugatan** — Lawsuit case data and detailed reports
- **Data Permohonan** — Petition/application case data by region
- **Laporan Putusan** — Court decision reports
- **Laporan Perceraian** — Divorce case reports (by region: HSU, Balangan)
- **Faktor Perceraian** — Divorce factor analysis
- **Perkara Gaib** — Absent party case tracking
- **Akta Cerai** — Divorce certificate issuance, delivery, validation, and late-certificate tracking
- **Delegasi** — Incoming and outgoing case delegations
- **Monitoring SIPP** — Daily dashboard, aging report, minutasi monitoring, performance metrics, Dirput anonimisasi upload tracking
- **Perkara Banding** — Appeal case tracking
- **LIPA-1** — Standard court reporting format

## Domain Context

- The system reads from a MySQL database (`sipp_4`) that mirrors the national SIPP court information system
- Reports are primarily read-only views with filtering by year, month, region, and case type
- Case types include: Cerai Gugat, Cerai Talak, Isbat Nikah, Dispensasi Kawin, Waris, Wakaf, Ekonomi Syariah
- Timezone: Asia/Jakarta (WIB)
