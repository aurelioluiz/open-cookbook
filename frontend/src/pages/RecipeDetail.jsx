import { useState, useEffect, useContext } from 'react';
import { AuthContext } from '../context/AuthContext';
import { useParams, useNavigate } from 'react-router-dom';
import axios from 'axios';

function RecipeDetail() {
  const { id } = useParams();
  const [recipe, setRecipe] = useState(null);
  const [error, setError] = useState('');
  const { user } = useContext(AuthContext);
  const navigate = useNavigate();

  useEffect(() => {
    if (!user) {
      navigate('/login');
      return;
    }
    fetchRecipe();
  }, [user, navigate, id]);

  const fetchRecipe = async () => {
    try {
      const res = await axios.get(`/api/recipes/${id}`);
      setRecipe(res.data);
    } catch (err) {
      setError('Erro ao carregar a receita.');
    }
  };

  const handleDelete = async () => {
    if (window.confirm('Tem certeza que deseja excluir esta receita?')) {
      try {
        await axios.delete(`/api/recipes/${id}`);
        navigate('/recipes');
      } catch (err) {
        setError('Erro ao excluir a receita.');
      }
    }
  };

  if (!recipe) return <div className="text-center text-2xl text-gray-900">Carregando...</div>;

  return (
    <div className="min-h-screen bg-gradient-to-br from-indigo-50 to-indigo-200 py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-3xl mx-auto">
        <div className="bg-white p-10 rounded-3xl shadow-2xl space-y-6 border border-gray-200">
          <h2 className="text-4xl font-extrabold text-gray-900 tracking-tight">{recipe.title}</h2>
          {error && (
            <p className="text-secondary text-base text-center font-semibold bg-red-100 py-3 rounded-lg flex items-center justify-center gap-2">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {error}
            </p>
          )}
          <p className="text-gray-700 text-lg leading-relaxed">{recipe.description}</p>
          <div className="flex space-x-4">
            <button
              onClick={() => navigate(`/recipes/edit/${id}`)}
              className="bg-gradient-to-r from-primary to-indigo-600 text-white px-6 py-3 rounded-xl font-bold text-lg hover:from-indigo-600 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition-all duration-300"
            >
              Editar
            </button>
            <button
              onClick={handleDelete}
              className="bg-gradient-to-r from-secondary to-red-600 text-white px-6 py-3 rounded-xl font-bold text-lg hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 transition-all duration-300"
            >
              Excluir
            </button>
          </div>
          <button
            onClick={() => navigate('/recipes')}
            className="text-primary hover:text-indigo-600 font-bold text-lg"
          >
            Voltar para Receitas
          </button>
        </div>
      </div>
    </div>
  );
}

export default RecipeDetail;