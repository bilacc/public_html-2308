# Project Setup Instructions

## Backend Setup
1. Navigate to the `backend` directory.
2. Run `npm install` to install dependencies.
3. Set `DATABASE_URL` in `.env` file with your new MySQL connection string.
4. Run `npx prisma generate` to generate Prisma client.
5. Run `npx prisma migrate deploy` to apply migrations to your MySQL database.
6. Import your old SQL dump (e.g., `nekretnina.sql`) into a temporary MySQL database (e.g. `property_baza`).
7. Run `ts-node scripts/migrateData.ts` to migrate data from the old database to the new Prisma database.
8. Run `npm run dev` to start the backend server.

## Frontend Setup
1. Navigate to the `frontend` directory.
2. Run `npm install` to install dependencies.
3. Run `npm start` to launch the React development server.
4. Open your browser and visit `http://localhost:3000`

## Notes
- Ensure the backend is running on port 4000 (default) before running the frontend.
- Ensure CORS is enabled in backend (already configured).


For any issues, contact support.