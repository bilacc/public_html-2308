// backend/scripts/migrateData.ts
import { PrismaClient } from '@prisma/client';
import mysql from 'mysql2/promise';

async function migrate() {
  const connection = await mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: 'Marisol89!',
    database: 'new_real_estate_db', // your old database
  });

  const prisma = new PrismaClient();

  // Fetch all records from old nekretnina table
  const [rows] = await connection.execute('SELECT * FROM nekretnina');

  // Insert into Prisma-managed database
  for (const row of rows as any[]) {
    const {
      id, ime, prezime, telefon, mobitel, e_mail, lokacija_id, adresa, status_id, vrsta_id,
      udaljenost, povrsina, stambenapovrsina, display, cijena, cijenatocka, ponuda_tjedna,
      new_built, must_sell, prvi_red_do_mora, pogled_na_more, bazen, broj_soba, broj_kupatila,
      datum, title_hr, title_en, title_ge, opis_hr, opis_en, opis_ge,
      PropertyImageURL0, PropertyImageURL1, PropertyImageURL2, PropertyImageURL3, PropertyImageURL4,
      PropertyImageURL5, PropertyImageURL6, PropertyImageURL7, PropertyImageURL8
    } = row;

    await prisma.nekretnina.upsert({
      where: { id: id as number },
      update: {},
      create: {
        id: id as number,
        ime, prezime, telefon, mobitel, e_mail, lokacija_id, adresa, status_id, vrsta_id,
        udaljenost, povrsina, stambenapovrsina, display, cijena, cijenatocka, ponuda_tjedna,
        new_built, must_sell, prvi_red_do_mora, pogled_na_more, bazen, broj_soba, broj_kupatila,
        datum, title_hr, title_en, title_ge, opis_hr, opis_en, opis_ge,
        PropertyImageURL0, PropertyImageURL1, PropertyImageURL2, PropertyImageURL3, PropertyImageURL4,
        PropertyImageURL5, PropertyImageURL6, PropertyImageURL7, PropertyImageURL8
      }
    });
  }

  await connection.end();
  await prisma.$disconnect();

  console.log('Migration completed');
}

migrate().catch(e => {
  console.error(e);
  process.exit(1);
});
