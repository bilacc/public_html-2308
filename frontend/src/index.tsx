// frontend/src/index.tsx
import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import { BrowserRouter } from 'react-router-dom';

const root = ReactDOM.createRoot(document.getElementById('root') as HTMLElement);
root.render(
  <React.StrictMode>
    <BrowserRouter>
      <App />
    </BrowserRouter>
  </React.StrictMode>
);

// frontend/src/App.tsx
import React from 'react';
import { Routes, Route } from 'react-router-dom';
import PropertyList from './components/PropertyList';
import PropertyDetail from './components/PropertyDetail';

const App: React.FC = () => {
  return (
    <div>
      <h1>Real Estate Listings</h1>
      <Routes>
        <Route path='/' element={<PropertyList />} />
        <Route path='/property/:id' element={<PropertyDetail />} />
      </Routes>
    </div>
  );
};

export default App;

// frontend/src/components/PropertyDetail.tsx
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

// frontend/src/components/PropertyList.tsx
import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';

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
            <Link to={`/property/${property.id}`}> <strong>{property.ime} {property.prezime}</strong></Link><br />
            Address: {property.adresa}<br />
            Price: {property.cijena} EUR
          </li>
        ))}
      </ul>
    </div>
  );
};

export default PropertyList;
