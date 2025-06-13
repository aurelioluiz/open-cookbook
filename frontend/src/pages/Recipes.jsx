import { useState, useEffect, useContext } from 'react';
import { AuthContext } from '../context/AuthContext';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { debounce } from 'lodash';

function Recipes() {
  const [recipes, setRecipes] = useState([]);
  const [title, setTitle] = useState('');
  const [description, setDescription] = useState('');
  const [search, setSearch] = useState('');
  const [editRecipe, setEditRecipe] = useState(null);
  const [error, setError] = useState('');
  const { user, loading, logout } = useContext(AuthContext);
  const navigate = useNavigate();

  useEffect(() => {
    if(!loading) {
      if (!user) {
        navigate('/login');
        return;
      }

      debouncedFetch();
      return () => debouncedFetch.cancel();
    }
  }, [loading, user, search]);

  const fetchRecipes = async () => {
    console.log('fetchRecipes');
    try {
      const res = await axios.get(`/api/recipes?search=${encodeURIComponent(search)}`);
      setRecipes(res.data);
    } catch (err) {
      setError('Erro ao carregar receitas.');
    }
  };

  const debouncedFetch = debounce(fetchRecipes, 300);

  const handleAddRecipe = async (e) => {
    e.preventDefault();
    try {
      await axios.post('/api/recipes', { title, description });
      setTitle('');
      setDescription('');
      fetchRecipes();
    } catch (err) {
      setError('Erro ao adicionar receita.');
    }
  };

  const handleEditRecipe = async (e) => {
    e.preventDefault();
    try {
      await axios.put(`/api/recipes/${editRecipe.id}`, { title, description });
      clearEditModal();
      fetchRecipes();
    } catch (err) {
      setError('Erro ao editar receita.');
    }
  };

  const handleDeleteRecipe = async (id) => {
    if (window.confirm('Tem certeza que deseja excluir esta receita?')) {
      try {
        await axios.delete(`/api/recipes/${id}`);
        fetchRecipes();
      } catch (err) {
        setError('Erro ao excluir receita.');
      }
    }
  };

  const openEditModal = (recipe) => {
    setEditRecipe(recipe);
    setTitle(recipe.title);
    setDescription(recipe.description);
  };

  const clearEditModal = () => {
    setEditRecipe(null);
    setTitle('');
    setDescription('');
  }

  if (loading)
    return

  return (
    <div className="min-h-screen bg-gradient-to-br from-indigo-50 to-indigo-200 py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-4xl mx-auto">
        <div className="flex justify-between items-center mb-12">
          <h2 className="text-5xl font-extrabold text-gray-900 tracking-tight">Minhas Receitas</h2>
          <button
            onClick={logout}
            className="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            Sair
          </button>
        </div>
        {error && (
          <p className="text-secondary text-base text-center font-semibold bg-red-100 py-3 rounded-lg flex items-center justify-center gap-2 mb-8">
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {error}
          </p>
        )}
        <div className="mb-8">
          <label className="block text-lg font-semibold text-gray-800 mb-2" htmlFor="search">
            Buscar Receitas
          </label>
          <input
            type="text"
            id="search"
            className="w-full px-4 py-2 border-2 border-gray-400 rounded-md bg-gray-50 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 shadow-inner"
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            placeholder="Digite o título, ingrediente ou categoria..."
          />
        </div>
        <form
          onSubmit={editRecipe ? handleEditRecipe : handleAddRecipe}
          className="bg-white p-10 rounded-3xl shadow-2xl mb-10 space-y-6 border border-gray-200 transform transition-all hover:shadow-3xl"
        >
          <h3 className="text-3xl font-bold text-gray-900 tracking-tight">
            {editRecipe ? 'Editar Receita' : 'Adicionar Receita'}
          </h3>
          <div>
            <label className="block text-lg font-semibold text-gray-800 mb-2" htmlFor="title">
              Título
            </label>
            <input
              type="text"
              id="title"
              className="w-full px-4 py-2 border-2 border-gray-400 rounded-md bg-gray-50 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 shadow-inner"
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              placeholder="Digite o título da receita"
              required
            />
          </div>
          <div>
            <label className="block text-lg font-semibold text-gray-800 mb-2" htmlFor="description">
              Descrição
            </label>
            <textarea
              id="description"
              className="w-full px-4 py-2 border-2 border-gray-400 rounded-md bg-gray-50 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 shadow-inner"
              value={description}
              onChange={(e) => setDescription(e.target.value)}
              placeholder="Descreva a receita"
              rows="6"
              required
            />
          </div>
          <div className="flex space-x-4">
            <button
              type="submit"
              className="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              {editRecipe ? 'Salvar' : 'Adicionar'}
            </button>
            {editRecipe && (
              <button
                type="button"
                onClick={() => clearEditModal()}
                className="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500"
              >
                Cancelar
              </button>
            )}
          </div>
        </form>
        <div className="space-y-6">
          {recipes.map((recipe) => (
            <div
              key={recipe.id}
              className="bg-white p-8 rounded-3xl shadow-2xl flex justify-between items-center border border-gray-200 transform transition-all hover:shadow-3xl"
            >
              <div className="space-y-2">
                <h4
                  className="text-2xl font-bold text-gray-900 tracking-tight cursor-pointer hover:text-primary"
                  onClick={() => navigate(`/recipes/${recipe.id}`)}
                >
                  {recipe.title}
                </h4>
                <p className="text-gray-700 text-base leading-relaxed">{recipe.description}</p>
              </div>
              <div className="flex space-x-4">
                <button
                  onClick={() => openEditModal(recipe)}
                  className="bg-yellow-500 text-white py-2 px-4 rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                >
                  Editar
                </button>
                <button
                  onClick={() => handleDeleteRecipe(recipe.id)}
                  className="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
                >
                  Excluir
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

export default Recipes;