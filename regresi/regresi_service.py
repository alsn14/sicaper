import pandas as pd
from sklearn.linear_model import LinearRegression
import sys
import json
from datetime import timedelta
import os
import numpy as np
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

# 1. Baca dua file
base_dir = os.path.dirname(os.path.abspath(__file__))
df_barang = pd.read_csv(os.path.join(base_dir, "data_service.csv"))
df_service = pd.read_csv(os.path.join(base_dir, "service_records_export.csv"))
df_service.rename(columns={"item_id": "id_barang"}, inplace=True)
df_service.columns = df_service.columns.str.strip()

# Debugging
# print("Data barang: ", df_barang.head())
# print("Data service: ", df_service.head())

# 2. Format datetime
df_barang['tanggal_pembelian'] = pd.to_datetime(df_barang['tanggal_pembelian'], errors='coerce')
df_service['tanggal_service'] = pd.to_datetime(df_service['tanggal_service'], errors='coerce')

# Debugging
# print("Tanggal pembelian: ", df_barang['tanggal_pembelian'].head())
# print("Tanggal service: ", df_service['tanggal_service'].head())

# 3. Ambil service terakhir per barang (jika ada)
latest_service = df_service.sort_values('tanggal_service').groupby('id_barang').last().reset_index()
latest_service = latest_service.rename(columns={
    'item_id': 'id_barang',
    'tanggal_service': 'tanggal_service_terakhir'
})

# Debugging
# print("Service terakhir: ", latest_service.head())

# 4. Gabungkan ke df_barang
df = pd.merge(
    df_barang.drop(columns=['tanggal_service_terakhir'], errors='ignore'),
    latest_service[['id_barang', 'tanggal_service_terakhir']],
    on='id_barang',
    how='left'
)

# Debugging
# print("Data gabungan: ", df.head())

# 5. Tambah kolom 'belum_diservice'
df['belum_diservice'] = df['tanggal_service_terakhir'].isna()

# 6. Gunakan tanggal service terakhir jika ada, kalau tidak pakai tanggal pembelian
df['tanggal_service_dipakai'] = df.apply(
    lambda row: row['tanggal_pembelian'] if pd.isna(row['tanggal_service_terakhir']) else row['tanggal_service_terakhir'],
    axis=1
)

# Debugging
# print("Tanggal service dipakai: ", df['tanggal_service_dipakai'].head())

# 7. Hitung selisih hari (hanya untuk yang pernah diservice)
df['selisih_hari'] = (df['tanggal_service_terakhir'] - df['tanggal_pembelian']).dt.days
train_df = df[df['selisih_hari'] > 0]

# Debugging
# print("Data training: ", train_df.head())

# 8. Siapkan data training
X_train = pd.get_dummies(train_df[['jenis_barang']])
y_train = train_df['selisih_hari']

# Debugging
# print("Data training X: ", X_train.head())
# print("Data training y: ", y_train.head())

# 9. Latih model regresi
model = LinearRegression()
model.fit(X_train, y_train)

# Debugging
# print("Model latih: ", model)

# 10. Siapkan semua data untuk prediksi
X_all = pd.get_dummies(df[['jenis_barang']])
X_all = X_all.reindex(columns=X_train.columns, fill_value=0)

# Debugging
# print("Data prediksi X: ", X_all.head())

# 11. Prediksi selisih hari dan tanggal service berikutnya
df['prediksi_selisih_hari'] = model.predict(X_all).round().astype(int)
df['prediksi_jadwal_service'] = df.apply(
    lambda row: row['tanggal_pembelian'] + pd.to_timedelta(row['prediksi_selisih_hari'], unit='D') if pd.isna(row['tanggal_service_terakhir']) 
    else row['tanggal_service_terakhir'] + pd.to_timedelta(max(1, row['prediksi_selisih_hari']), unit='D'), axis=1
)

# Debugging
# print("Prediksi selisih hari: ", df['prediksi_selisih_hari'].head())
# print("Prediksi service berikutnya: ", df['prediksi_jadwal_service'].head())

# 12. Format hasil output
hasil = df[['id_barang', 'nama_barang', 'jenis_barang', 'tanggal_pembelian',
            'tanggal_service_terakhir', 'prediksi_jadwal_service', 'belum_diservice']].copy()
hasil['tanggal_pembelian'] = pd.to_datetime(hasil['tanggal_pembelian']).dt.strftime('%Y-%m-%d')
hasil['tanggal_service_terakhir'] = pd.to_datetime(hasil['tanggal_service_terakhir'], errors='coerce').dt.strftime('%Y-%m-%d')
hasil['prediksi_jadwal_service'] = pd.to_datetime(hasil['prediksi_jadwal_service'], errors='coerce').dt.strftime('%Y-%m-%d')
hasil = hasil.dropna(subset=['id_barang', 'prediksi_jadwal_service'])
hasil['id_barang'] = hasil['id_barang'].astype(int)

# Debugging
# print("Hasil akhir: ", hasil.head())

# 13. Cetak JSON hasil akhir ke Laravel
print(hasil.to_json(orient='records'))

# ✅ Tambahkan ini untuk membuat output
output = hasil.replace({np.nan: None}).to_dict(orient='records')

# Tambahkan ini di atas
base_dir = os.path.dirname(os.path.abspath(__file__))

# Di bagian akhir (simpan ke folder ai/)
with open(os.path.join(base_dir, 'prediksi_service.json'), 'w') as f:
    json.dump(output, f, indent=2)

print("✅ File prediksi_service.json berhasil ditulis ulang.")