import pandas as pd
from sklearn.cluster import KMeans
from datetime import datetime
import json
import shutil

# Load prediksi dari regresi
with open('../ai/prediksi_service.json', 'r') as f:
    data = json.load(f)

df = pd.DataFrame(data)

# Konversi kolom tanggal
today = datetime.today()
df['tanggal_pembelian'] = pd.to_datetime(df['tanggal_pembelian'])
df['tanggal_service_terakhir'] = pd.to_datetime(df['tanggal_service_terakhir'], errors='coerce')
df['prediksi_jadwal_service'] = pd.to_datetime(df['prediksi_jadwal_service'], errors='coerce')  # ganti nama kolom agar sesuai

# Buat fitur numerik untuk clustering
df['tanggal_dipakai'] = df.apply(
    lambda row: row['tanggal_pembelian'] if pd.isna(row['tanggal_service_terakhir']) else row['tanggal_service_terakhir'],
    axis=1
)
df['days_since_last_service'] = (today - df['tanggal_dipakai']).dt.days
df['days_to_next_service'] = (df['prediksi_jadwal_service'] - today).dt.days
df['umur_barang'] = (today - df['tanggal_pembelian']).dt.days

# Tangani nilai negatif
df['days_to_next_service'] = df['days_to_next_service'].apply(lambda x: max(x, 0))
df['days_since_last_service'] = df['days_since_last_service'].apply(lambda x: max(x, 0))

# Clustering
features = df[['days_to_next_service', 'umur_barang', 'days_since_last_service']]
kmeans = KMeans(n_clusters=3, random_state=42, n_init='auto')
df['cluster'] = kmeans.fit_predict(features)

# Interpretasi hasil clustering
cluster_priority = df.groupby('cluster')['days_to_next_service'].mean().sort_values().reset_index()
cluster_priority['label'] = ['Prioritas Tinggi', 'Prioritas Sedang', 'Prioritas Rendah']
cluster_label_map = dict(zip(cluster_priority['cluster'], cluster_priority['label']))
df['rekomendasi'] = df['cluster'].map(cluster_label_map)

# Kolom output akhir
hasil = df[[
    'nama_barang', 'jenis_barang', 'tanggal_pembelian',
    'tanggal_service_terakhir', 'prediksi_jadwal_service', 'rekomendasi'
]]

# Simpan ke JSON
hasil.to_json('rekomendasi_service.json', orient='records', date_format='iso')

print("✅ Rekomendasi berhasil disimpan ke 'rekomendasi_service.json'")

shutil.copy("rekomendasi_service.json", "../public/rekomendasi_service.json")