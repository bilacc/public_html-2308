import React, { useEffect, useState } from 'react';

interface Property {
  id: number;
  ime: string;
  prezime: string;
  adresa: string;
  cijena: string;
}

const PropertyList: React.FC = () => {
  const [properties, setProperties] = useState<Property[]>([]);

  useEffect(() => {
    fetch('http://localhost:4000/api/properties')
      .then(res => res.json())
      .then(data => setProperties(data))
      .catch(console.error);
  }, []);

  return (
    <div>
      <ul>
        {properties.map(property => (
          <li key={property.id}>
            <strong>{property.ime} {property.prezime}</strong><br />
            Address: {property.adresa}<br />
            Price: {property.cijena} EUR
          </li>
        ))}
      </ul>
    </div>
  );
};

export default PropertyList;
