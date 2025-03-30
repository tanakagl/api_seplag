import { FormEvent } from 'react'
import { Head, useForm } from '@inertiajs/react'
import { PageProps } from '@inertiajs/core'
import DatePicker, { registerLocale } from 'react-datepicker'
import { ptBR } from 'date-fns/locale'
import "react-datepicker/dist/react-datepicker.css"

export default function Create({ errors }: PageProps & { errors: Record<string, string> }) {

  registerLocale('pt-BR', ptBR)

  const { data, setData, post, processing } = useForm({
    pes_nome: '',
    pes_data_nascimento: '',
    pes_sexo: '',
    pes_mae: '',
    pes_pai: '',
    se_matricula: '',
  });

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    post(route('servidores.efetivo.store'));
  };

  const handleDateChange = (date: Date | null) => {
    if (date) {
      const formattedDate = date.toISOString().split('T')[0];
      setData('pes_data_nascimento', formattedDate);
    } else {
      setData('pes_data_nascimento', '');
    }
  };

  const getDateValue = () => {
    if (!data.pes_data_nascimento) return null;
    const date = new Date(data.pes_data_nascimento);
    return isNaN(date.getTime()) ? null : date;
  };

  return (
    <>
      <Head title="Criar Servidor Efetivo" />
      
      <div className="container mx-auto py-8">
        <div className="max-w-2xl mx-auto">
          <h1 className="text-2xl font-semibold mb-6">Adicionar Servidor Efetivo</h1>
          
          <form onSubmit={handleSubmit}>
            <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
              <div className="mb-4">
                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="pes_nome">
                  Nome
                </label>
                <input
                  type="text"
                  id="pes_nome"
                  value={data.pes_nome}
                  onChange={e => setData('pes_nome', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
                {errors.pes_nome && <p className="text-red-500 text-xs mt-1">{errors.pes_nome}</p>}
              </div>
              
              <div className="mb-4">
                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="pes_data_nascimento">
                  Data de Nascimento
                </label>
                <DatePicker
                  id="pes_data_nascimento"
                  selected={getDateValue()}
                  onChange={handleDateChange}
                  dateFormat="dd/MM/yyyy"
                  locale="pt-BR"
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  placeholderText="DD/MM/AAAA"
                  required
                />
                {errors.pes_data_nascimento && <p className="text-red-500 text-xs mt-1">{errors.pes_data_nascimento}</p>}
              </div>
              
              <div className="mb-4">
                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="pes_sexo">
                  Sexo
                </label>
                <select
                  id="pes_sexo"
                  value={data.pes_sexo}
                  onChange={e => setData('pes_sexo', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  required
                >
                  <option value="">Selecione</option>
                  <option value="M">Masculino</option>
                  <option value="F">Feminino</option>
                </select>
                {errors.pes_sexo && <p className="text-red-500 text-xs mt-1">{errors.pes_sexo}</p>}
              </div>
              
              <div className="mb-4">
                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="pes_mae">
                  Nome da Mãe
                </label>
                <input
                  type="text"
                  id="pes_mae"
                  value={data.pes_mae}
                  onChange={e => setData('pes_mae', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
                {errors.pes_mae && <p className="text-red-500 text-xs mt-1">{errors.pes_mae}</p>}
              </div>
              
              <div className="mb-4">
                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="pes_pai">
                  Nome do Pai
                </label>
                <input
                  type="text"
                  id="pes_pai"
                  value={data.pes_pai}
                  onChange={e => setData('pes_pai', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
                {errors.pes_pai && <p className="text-red-500 text-xs mt-1">{errors.pes_pai}</p>}
              </div>
              
              <div className="mb-4">
                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="se_matricula">
                  Matrícula
                </label>
                <input
                  type="text"
                  id="se_matricula"
                  value={data.se_matricula}
                  onChange={e => setData('se_matricula', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
                {errors.se_matricula && <p className="text-red-500 text-xs mt-1">{errors.se_matricula}</p>}
              </div>
              
              <div className="flex items-center justify-end mt-6">
                <button
                  type="button"
                  onClick={() => window.history.back()}
                  className="mr-4 px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  disabled={processing}
                  className="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                  {processing ? 'Salvando...' : 'Salvar'}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </>
  );
}
