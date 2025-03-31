import { Head } from '@inertiajs/react'
import { PageProps } from '@inertiajs/core'
import DatePicker from 'react-datepicker'
import "react-datepicker/dist/react-datepicker.css"
import useFormulario from './hooks/useFormulario'

export default function Create({ errors }: PageProps & { errors: Record<string, string> }) {

  const { data, setData, post, processing, handleDateChange, getDateValue, handleAdmissaoDateChange, getAdmissaoDateValue, handleDemissaoDateChange, getDemissaoDateValue } = useFormulario()

  return (
    <>
      <Head title="Criar Servidor Temporário" />

      <div className="container mx-auto py-8">
        <div className="max-w-2xl mx-auto">
          <h1 className="text-2xl font-semibold mb-6">Adicionar Servidor Temporário</h1>

          <form onSubmit={post}>
            <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
              <h2 className="text-xl font-semibold mb-4">Informações Pessoais</h2>
              
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
                  isClearable
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

              <h2 className="text-xl font-semibold mb-4 mt-6">Informações do Contrato</h2>

              <div className="mb-4">
                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="st_data_admissao">
                  Data de Admissão
                </label>
                <DatePicker
                  id="st_data_admissao"
                  selected={getAdmissaoDateValue()}
                  onChange={handleAdmissaoDateChange}
                  dateFormat="dd/MM/yyyy"
                  locale="pt-BR"
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  placeholderText="DD/MM/AAAA"
                  isClearable
                  required
                />
                {errors.st_data_admissao && <p className="text-red-500 text-xs mt-1">{errors.st_data_admissao}</p>}
              </div>

              <div className="mb-4">
                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="st_data_demissao">
                  Data de Demissão
                </label>
                <DatePicker
                  id="st_data_demissao"
                  selected={getDemissaoDateValue()}
                  onChange={handleDemissaoDateChange}
                  dateFormat="dd/MM/yyyy"
                  locale="pt-BR"
                  className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  placeholderText="DD/MM/AAAA"
                  isClearable
                />
                <p className="text-gray-500 text-xs mt-1">Deixe em branco se o servidor ainda estiver ativo</p>
                {errors.st_data_demissao && <p className="text-red-500 text-xs mt-1">{errors.st_data_demissao}</p>}
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
