import React from 'react'
import { Head, useForm } from '@inertiajs/react'
import { PageProps } from '@inertiajs/core'

interface Pessoa {
    pes_id: number;
    pes_nome: string;
}

interface Cidade {
    cid_id: number;
    nome: string;
    estado_id: number;
}

interface CreateForPessoaProps extends PageProps {
    pessoa: Pessoa;
    cidades: Cidade[];
}

export default function CreateForPessoa({ pessoa, cidades, errors }: CreateForPessoaProps & { errors: Record<string, string> }) {
    const { data, setData, post, processing } = useForm({
        end_tipo_logradouro: '',
        end_logradouro: '',
        end_numero: '',
        end_bairro: '',
        cid_id: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('pessoa.endereco.store', pessoa.pes_id));
    };

    return (
        <>
            <Head title={`Adicionar Endereço para ${pessoa.pes_nome}`} />

            <div className="container mx-auto py-8">
                <div className="max-w-2xl mx-auto">
                    <h1 className="text-2xl font-semibold mb-6">Adicionar Endereço para {pessoa.pes_nome}</h1>

                    <form onSubmit={handleSubmit}>
                        <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                            <div className="mb-4">
                                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="end_tipo_logradouro">
                                    Tipo de Logradouro
                                </label>
                                <input
                                    type="text"
                                    id="end_tipo_logradouro"
                                    value={data.end_tipo_logradouro}
                                    onChange={e => setData('end_tipo_logradouro', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Ex: Rua, Avenida, Alameda"
                                    required
                                />
                                {errors.end_tipo_logradouro && <p className="text-red-500 text-xs mt-1">{errors.end_tipo_logradouro}</p>}
                            </div>

                            <div className="mb-4">
                                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="end_logradouro">
                                    Logradouro
                                </label>
                                <input
                                    type="text"
                                    id="end_logradouro"
                                    value={data.end_logradouro}
                                    onChange={e => setData('end_logradouro', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Nome do logradouro"
                                    required
                                />
                                {errors.end_logradouro && <p className="text-red-500 text-xs mt-1">{errors.end_logradouro}</p>}
                            </div>

                            <div className="mb-4">
                                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="end_numero">
                                    Número
                                </label>
                                <input
                                    type="text"
                                    id="end_numero"
                                    value={data.end_numero}
                                    onChange={e => setData('end_numero', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Número"
                                />
                                {errors.end_numero && <p className="text-red-500 text-xs mt-1">{errors.end_numero}</p>}
                            </div>

                            <div className="mb-4">
                                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="end_bairro">
                                    Bairro
                                </label>
                                <input
                                    type="text"
                                    id="end_bairro"
                                    value={data.end_bairro}
                                    onChange={e => setData('end_bairro', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Bairro"
                                    required
                                />
                                {errors.end_bairro && <p className="text-red-500 text-xs mt-1">{errors.end_bairro}</p>}
                            </div>

                            <div className="mb-4">
                                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="cid_id">
                                    Cidade
                                </label>
                                <select
                                    id="cid_id"
                                    value={data.cid_id}
                                    onChange={e => setData('cid_id', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                >
                                    <option value="">Selecione uma cidade</option>
                                    {cidades.map((cidade) => (
                                        <option key={cidade.cid_id} value={cidade.cid_id}>
                                            {cidade.nome}
                                        </option>
                                    ))}
                                </select>
                                {errors.cid_id && <p className="text-red-500 text-xs mt-1">{errors.cid_id}</p>}
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
