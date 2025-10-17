import React from 'react';
import { Routes, Route } from 'react-router-dom';
import PropertyList from './components/PropertyList';
import PropertyDetail from './components/PropertyDetail';

const App: React.FC = () => (
  <div>
    <h1>Real Estate Listings</h1>
    <Routes>
      <Route path='/' element={<PropertyList />} />
      <Route path='/property/:id' element={<PropertyDetail />} />
    </Routes>
  </div>
);

export default App;
