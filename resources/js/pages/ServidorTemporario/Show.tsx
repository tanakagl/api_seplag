import { Head, Link } from '@inertiajs/react'
import { PageProps } from '@inertiajs/core'

interface Pessoa {
    pes_id: number;
    pes_nome: string;
    pes_data_nascimento: string;
    pes_sexo: string;
    pes_mae: string;
    pes_pai: string;
}

interface ServidorTemporario {
    pes_id: number;
    st_data_admissao: string;
    st_data_demissao: string | null;
    pessoa: Pessoa;
    pessoa_enderecos?: any[];
    pessoa_lotacoes?: any[];
}

interface ShowProps extends PageProps {
    servidor: ServidorTemporario;
}

export default function Show({ servidor }: ShowProps) {
    return (
        <>
            <Head title={`Servidor: ${servidor.pessoa.pes_nome}`} />

            <div className="container mx-auto py-8">
                <div className="max-w-4xl mx-auto">
                    <div className="flex justify-between items-center mb-6">
                        <h1 className="text-2xl font-semibold">Detalhes do Servidor Temporário</h1>
                        <div className="flex space-x-4">
                            <Link
                                href={route('servidores.temporario.edit', servidor.pes_id)}
                                className="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded"
                            >
                                Editar
                            </Link>
                            <Link
                                href={route('servidores.temporario.index')}
                                className="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded"
                            >
                                Voltar
                            </Link>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden mb-6">
                        <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 className="text-xl font-semibold">Informações Pessoais</h2>
                        </div>
                        <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Nome:</p>
                                <p className="font-medium">{servidor.pessoa.pes_nome}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Data de Nascimento:</p>
                                <p className="font-medium">{new Date(servidor.pessoa.pes_data_nascimento).toLocaleDateString()}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Sexo:</p>
                                <p className="font-medium">{servidor.pessoa.pes_sexo === 'M' ? 'Masculino' : 'Feminino'}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Nome da Mãe:</p>
                                <p className="font-medium">{servidor.pessoa.pes_mae}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Nome do Pai:</p>
                                <p className="font-medium">{servidor.pessoa.pes_pai}</p>
                            </div>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden mb-6">
                        <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 className="text-xl font-semibold">Informações do Contrato</h2>
                        </div>
                        <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Data de Admissão:</p>
                                <p className="font-medium">{new Date(servidor.st_data_admissao).toLocaleDateString()}</p>
                            </div>
                            <div>
                                <p className="text-gray-500 dark:text-gray-400 text-sm">Data de Demissão:</p>
                                <p className="font-medium">
                                    {servidor.st_data_demissao 
                                        ? new Date(servidor.st_data_demissao).toLocaleDateString() 
                                        : 'Não informada'}
                                </p>
                            </div>
                        </div>
                    </div>

                    {servidor.pessoa_enderecos && servidor.pessoa_enderecos.length > 0 && (
                        <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden mb-6">
                            <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 className="text-xl font-semibold">Endereços</h2>
                            </div>
                            <div className="p-6">
                                {servidor.pessoa_enderecos?.map((endereco, index) => (
                                    <div key={index} className="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                        <p className="font-medium mb-2">Endereço {index + 1}</p>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <p className="text-gray-500 dark:text-gray-400 text-sm">Logradouro:</p>
                                                <p>{endereco.logradouro}</p>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {servidor.pessoa_lotacoes && servidor.pessoa_lotacoes.length > 0 && (
                        <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 className="text-xl font-semibold">Lotações</h2>
                            </div>
                            <div className="p-6">
                                {servidor.pessoa_lotacoes?.map((lotacao, index) => (
                                    <div key={index} className="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                        <p className="font-medium mb-2">Lotação {index + 1}</p>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <p className="text-gray-500 dark:text-gray-400 text-sm">Unidade:</p>
                                                <p>{lotacao.unidade?.uni_nome || 'Não informada'}</p>
                                            </div>
                                            <div>
                                                <p className="text-gray-500 dark:text-gray-400 text-sm">Data de Início:</p>
                                                <p>{lotacao.data_inicio ? new Date(lotacao.data_inicio).toLocaleDateString() : 'Não informada'}</p>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
