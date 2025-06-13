import { useState, useContext } from 'react';
import { AuthContext } from '../context/AuthContext';
import { Link, useNavigate } from 'react-router-dom';

function Register() {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const { register } = useContext(AuthContext);
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await register(name, email, password);
      navigate('/recipes');
    } catch (err) {
      setError('Erro ao registrar. Verifique os dados e tente novamente.');
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
      <div className="bg-white p-10 rounded-2xl shadow-xl w-full max-w-md space-y-8 transform transition-all hover:shadow-2xl">
        <h2 className="text-3xl font-bold text-gray-900 text-center tracking-tight">Registrar</h2>
        {error && <p className="text-red-600 text-sm text-center font-medium bg-red-50 py-2 rounded-lg">{error}</p>}
        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <label className="block text-sm font-semibold text-gray-700 mb-2" htmlFor="name">
              Nome
            </label>
            <input
              type="text"
              id="name"
              className="w-full py-2 px-4 border-2 border-gray-300 rounded-md bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all duration-300"
              value={name}
              onChange={(e) => setName(e.target.value)}
              required
            />
          </div>
          <div>
            <label className="block text-sm font-semibold text-gray-700 mb-2" htmlFor="email">
              E-mail
            </label>
            <input
              type="email"
              id="email"
              className="w-full py-2 px-4 border-2 border-gray-300 rounded-md bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all duration-300"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
          </div>
          <div>
            <label className="block text-sm font-semibold text-gray-700 mb-2" htmlFor="password">
              Senha
            </label>
            <input
              type="password"
              id="password"
              className="w-full py-2 px-4 border-2 border-gray-300 rounded-md bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all duration-300"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
          </div>
          <button
            type="submit"
            className="w-full bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500"
          >
            Registrar
          </button>
        </form>
        <p className="text-center text-sm text-gray-600 font-medium">
          Já tem uma conta?{' '}
          <Link to="/login" className="text-indigo-600 hover:text-indigo-800 font-semibold transition-colors">
            Faça login
          </Link>
        </p>
      </div>
    </div>
  );
}

export default Register;