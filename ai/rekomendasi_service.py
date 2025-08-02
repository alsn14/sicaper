import pandas as pd
from sklearn.cluster import KMeans
from datetime import datetime
import json
import shutil
import os

# Load prediksi dari regresi
with open('../ai/prediksi_service.json', 'r') as f:
    data = json.load(f)

df = pd.DataFrame(data)

# Konversi tanggal
today = datetime.today()
df['tanggal_pembelian'] = pd.to_datetime(df['tanggal_pembelian'])
df['tanggal_service_terakhir'] = pd.to_datetime(df['tanggal_service_terakhir'], errors='coerce')
df['prediksi_jadwal_service'] = pd.to_datetime(df['prediksi_jadwal_service'], errors='coerce')

# Tanggal yang dipakai untuk menghitung umur
df['tanggal_dipakai'] = df.apply(
    lambda row: row['tanggal_pembelian'] if pd.isna(row['tanggal_service_terakhir']) else row['tanggal_service_terakhir'],
    axis=1
)

# Fitur untuk clustering
df['days_since_last_service'] = (today - df['tanggal_dipakai']).dt.days.clip(lower=0)
df['days_to_next_service'] = (df['prediksi_jadwal_service'] - today).dt.days.clip(lower=0)
df['umur_barang'] = (today - df['tanggal_pembelian']).dt.days.clip(lower=0)
print("Data setelah menambahkan fitur:")
print(df[['nama_barang', 'days_since_last_service', 'days_to_next_service', 'umur_barang']].head())
# Clustering dengan KMeans
features = df[['days_to_next_service', 'umur_barang', 'days_since_last_service']]
kmeans = KMeans(n_clusters=3, random_state=42, n_init='auto')
df['cluster'] = kmeans.fit_predict(features)
print("Cluster summary:")
print(df['cluster'].value_counts())
# Interpretasi label cluster (yang paling dekat jadi "tinggi")
cluster_priority = df.groupby('cluster')['days_to_next_service'].mean().sort_values().reset_index()
cluster_priority['label'] = ['Prioritas Tinggi', 'Prioritas Sedang', 'Prioritas Rendah']
cluster_map = dict(zip(cluster_priority['cluster'], cluster_priority['label']))
df['rekomendasi'] = df['cluster'].map(cluster_map)

print("Mapping cluster ke prioritas:")
print(cluster_priority)

# 🔧 Koreksi dengan aturan interval jenis barang
intervals = {
    'Komputer': 6,
    'Printer': 6,
    'Scanner': 6,
    'Hardware Output': 12,
    'AC': 6,
    'Transportasi': 6,
    'Perabotan Kantor': 12,
    'Jaringan': 8,
    'Alat Pengolah Data': 6
}

def koreksi_prioritas(row):
    jenis = row['jenis_barang']
    interval_bulan = intervals.get(jenis, 6)  # default 6 bulan
    batas_service = row['tanggal_dipakai'] + pd.DateOffset(months=interval_bulan)
    days_to_next = row['days_to_next_service']

    # Naik ke prioritas tinggi kalau:
    if today > batas_service or days_to_next <= 30:
        return 'Prioritas Tinggi'
    
    # Turunkan ke Prioritas Rendah jika:
    if days_to_next >= 180 and row['days_since_last_service'] <= 60:
        return 'Prioritas Rendah'
    
    return row['rekomendasi']  # biarkan hasil AI kalau masih wajar



df['rekomendasi'] = df.apply(koreksi_prioritas, axis=1)

# Kolom akhir
hasil = df[[
    'nama_barang', 'jenis_barang', 'tanggal_pembelian',
    'tanggal_service_terakhir', 'prediksi_jadwal_service', 'rekomendasi'
]]

# Simpan ke JSON
hasil.to_json('rekomendasi_service.json', orient='records', date_format='iso')
print("Rekomendasi berhasil disimpan ke 'rekomendasi_service.json'")

base_dir = os.path.dirname(os.path.abspath(__file__))
src_path = os.path.join(base_dir, "rekomendasi_service.json")
dst_path = os.path.abspath(os.path.join(base_dir, "../public/rekomendasi_service.json"))

# Hindari copy kalau file sama
if os.path.abspath(src_path) != os.path.abspath(dst_path):
    shutil.copy(src_path, dst_path)

