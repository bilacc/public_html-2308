import React, { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';

interface Property {
  id: number;
  ime: string;
  prezime: string;
  adresa: string;
  cijena: string;
  opis_en: string;
}

const PropertyDetail: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const [property, setProperty] = useState<Property | null>(null);

  useEffect(() => {
    if (id) {
      fetch(`http://localhost:4000/api/properties/${id}`)
        .then(res => res.json())
        .then(data => setProperty(data))
        .catch(console.error);
    }
  }, [id]);

  if (!property) {
    return <div>Loading...</div>;
  }

  return (
    <div>
      <h2>{property.ime} {property.prezime}</h2>
      <p><strong>Address:</strong> {property.adresa}</p>
      <p><strong>Price:</strong> {property.cijena} EUR</p>
      <p><strong>Description:</strong> {property.opis_en}</p>
      <Link to="/">Back to list</Link>
    </div>
  );
};

export default PropertyDetail;
