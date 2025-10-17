import express, { Request, Response } from 'express';
import cors from 'cors';
import { PrismaClient } from '@prisma/client';

const app = express();
const port = process.env.PORT || 4000;
const prisma = new PrismaClient();

app.use(cors());
app.use(express.json());

// Get all properties
app.get('/api/properties', async (req: Request, res: Response) => {
  const properties = await prisma.nekretnina.findMany();
  res.json(properties);
});

// Get single property by id
app.get('/api/properties/:id', async (req: Request, res: Response) => {
  const id = Number(req.params.id);
  const property = await prisma.nekretnina.findUnique({ where: { id } });
  if (property) {
    res.json(property);
  } else {
    res.status(404).json({ error: 'Property not found' });
  }
});

app.listen(port, () => {
  console.log(`Server running on http://localhost:${port}`);
});
