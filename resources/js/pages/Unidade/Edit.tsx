import React from 'react'
import { Head, useForm } from '@inertiajs/react'
import { PageProps } from '@inertiajs/core'

interface Unidade {
    unid_id: number;
    unid_nome: string;
    unid_sigla: string;
}

interface EditProps extends PageProps {
    unidade: Unidade;
}

export default function Edit({ unidade, errors }: EditProps & { errors: Record<string, string> }) {
    const { data, setData, put, processing } = useForm({
        unid_nome: unidade.unid_nome || '',
        unid_sigla: unidade.unid_sigla || '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('unidade.update', unidade.unid_id));
    };

    return (
        <>
            <Head title="Editar Unidade" />

            <div className="container mx-auto py-8">
                <div className="max-w-2xl mx-auto">
                    <h1 className="text-2xl font-semibold mb-6">Editar Unidade</h1>

                    <form onSubmit={handleSubmit}>
                        <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                            <div className="mb-4">
                            <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="unid_nome">
                                    Nome da Unidade
                                </label>
                                <input
                                    type="text"
                                    id="unid_nome"
                                    value={data.unid_nome}
                                    onChange={e => setData('unid_nome', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                />
                                {errors.unid_nome && <p className="text-red-500 text-xs mt-1">{errors.unid_nome}</p>}
                            </div>

                            <div className="mb-4">
                                <label className="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" htmlFor="unid_sigla">
                                    Sigla
                                </label>
                                <input
                                    type="text"
                                    id="unid_sigla"
                                    value={data.unid_sigla}
                                    onChange={e => setData('unid_sigla', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                />
                                {errors.unid_sigla && <p className="text-red-500 text-xs mt-1">{errors.unid_sigla}</p>}
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
